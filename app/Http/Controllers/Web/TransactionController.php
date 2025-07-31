<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ProductVariant;
use App\Models\StockAmount;
use App\Models\StockAmountItem;
use App\Models\StockTransaction;
use App\Models\StockType;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::with([
                'customer:id,name,address',
                'seller:id,name',
                'items',
                'items.productVariant:id,code,product_id,other_code',
                'items.productVariant.product:id,name'
            ])
            ->when($request->search, function($query) use ($request) {
                $query->where('invoice', 'like', '%' . $request->search . '%');
            })
            ->when($request->customer_id, function($query) use ($request) {
                $query->where('customer_id', $request->customer_id);
            })
            ->when($request->seller_id, function($query) use ($request) {
                $query->where('seller_id', $request->seller_id);
            })
            ->when($request->payment_type, function($query) use ($request) {
                $query->where('payment_type', $request->payment_type);
            })
            ->when($request->status, function($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->product_variant_id, function($query) use ($request) {
                $query->whereHas('items', function($q) use ($request) {
                    $q->where('product_variant_id', $request->product_variant_id);
                });
            })
            ->when($request->start_date, function($query) use ($request) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
            })
            ->when($request->end_date, function($query) use ($request) {
                $query->whereDate('transaction_date', '<=', $request->end_date);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query());

        $customers = Customer::orderBy('name')->get(['id', 'name', 'code']);
        $productVariants = ProductVariant::with('product')->orderBy('code')->get(['id', 'code', 'product_id']);
        
        $sellerRole = DB::table('user_roles')->where('code', 'SALES')->first();
        $sellers = User::where('role_id', $sellerRole->id)
                    ->orderBy('name')
                    ->get(['id', 'name']);

        return view('transactions.index', compact(
            'transactions',
            'customers',
            'productVariants',
            'sellers'
        ));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $products = ProductVariant::with('product')->orderBy('code')->get(['id', 'code', 'product_id']);
        $stockTypes = StockType::all(); // Add this line

        $sellerRole = DB::table('user_roles')->where('code', 'SALES')->first();
        $sellers = User::where('role_id', $sellerRole->id)
                    ->orderBy('name')
                    ->get(['id', 'name']);

        return view('transactions.create', compact('customers', 'products', 'sellers'));
    }

    public function store(Request $request)
    {
        $items = [];
        foreach ($request->items as $key => $item) {
            if (is_array($item) && isset($item['product_variant_id']) && isset($item['quantity'])) {
                $items[] = [
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                    'stock_type_id' => $item['stock_type_id'] ?? null
                ];
            }
        }
        $request->merge(['items' => $items]);

        // Validate request
        $validator = Validator::make($request->all(), [
            'invoice' => 'required|string|max:50|unique:transactions',
            'deal_price' => 'required|numeric|min:0',
            'customer_id' => 'required|exists:customers,id',
            'seller_id' => 'required|exists:users,id',
            'payment_type' => 'required|in:installment,cash',
            'transaction_date' => 'required|date',
            'is_dp' => 'nullable|string',
            'dp_amount' => 'required_if:is_dp,1|nullable|string',
            'is_tempo' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.stock_type_id' => 'required|exists:stock_types,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Automatically set status based on payment type
            if ($request->payment_type === 'cash') {
                $status = $request->is_tempo ? 'pending' : 'paid';
            } else {
                $status = 'pending';
            }

            // Create transaction
            $transaction = Transaction::create([
                'invoice' => $request->invoice,
                'deal_price' => $request->deal_price,
                'customer_id' => $request->customer_id,
                'seller_id' => $request->seller_id,
                'payment_type' => $request->payment_type,
                'transaction_date' => $request->transaction_date,
                'is_tempo' => $request->is_tempo,
                'status' => $status,
            ]);
            
            if ($request->is_tempo) {
                $tempoAt = Carbon::parse($request->transaction_date)->addMonth();

                $transaction->outstanding()->create([
                    'outstanding_amount' => $request->deal_price,
                ]);

                $transaction->update([
                    'tempo_at' => $tempoAt,
                ]);
            }

            // Create transaction items and reduce stock
            foreach ($request->items as $item) {
                $variant = ProductVariant::find($item['product_variant_id']);
                $seller = User::with('vehicle')->find($request->seller_id);
                $warehouseId = $seller->vehicle->warehouse_id ?? null;
                
                if (!$variant) {
                    throw new Exception("Product variant not found: ".$item['product_variant_id']);
                }

                // Create transaction item
                $transaction->items()->create([
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                    'stock_type_id' => $item['stock_type_id'],
                    'snapshot_name' => $variant->code,
                    'snapshot_price' => $request->deal_price,
                ]);

                // Reduce stock amount
                if ($item['stock_type_id']) {
                    // Find the stock amount for this product variant
                    $stockAmount = StockAmount::where('warehouse_id', $warehouseId)
                        ->first();

                    if (!$stockAmount) {
                        throw new Exception("Stock not found for product variant: ".$item['product_variant_id']);
                    }

                    // Find the specific stock type item
                    $stockAmountItem = StockAmountItem::where('stock_amount_id', $stockAmount->id)
                        ->where('stock_type_id', $item['stock_type_id'])
                        ->first();

                    if (!$stockAmountItem) {
                        throw new Exception("Stock type not found for product variant: ".$item['product_variant_id']);
                    }

                    // Check if enough stock is available
                    if ($stockAmountItem->amount < $item['quantity']) {
                        throw new Exception("Insufficient stock for product variant: ".$variant->code);
                    }

                     // Get quantities before update
                    $quantityBefore = $stockAmountItem->amount;
                    $quantityAfter = $quantityBefore - $item['quantity'];

                    // Create stock transaction record
                    StockTransaction::create([
                        'stock_amount_id' => $stockAmount->id,
                        'warehouse_id' => $warehouseId,
                        'quantity' => $item['quantity'],
                        'type' => 'out',
                        'destination' => 'sold',
                        'transaction_id' => $transaction->id,
                        'quantity_before' => $quantityBefore,
                        'quantity_after' => $quantityAfter,
                        'user_id' => $request->seller_id,
                        'note' => 'Sold via transaction',
                        'created_at' => $transaction->transaction_date,
                    ]);

                    // Reduce the stock
                    $stockAmountItem->decrement('amount', $item['quantity']);
                    $stockAmount->decrement('total_amount', $item['quantity']);
                }
            }

            // If credit payment, create installment and outstanding records
            if ($request->payment_type === 'installment') {
                $transaction->outstanding()->create([
                    'outstanding_amount' => $request->deal_price,
                ]);
            }

            if ($request->is_dp) {
                $transaction->installments()->create([
                    'installment_amount' => $request->dp_amount,
                    'payment_date' => $request->transaction_date,
                ]);

                $transaction->outstanding()->update([
                    'outstanding_amount' => $request->deal_price - $request->dp_amount
                ]);
            }

            DB::commit();

            return redirect()->route('transactions.index')->with('success', 'Transaction created successfully');

        } catch (Exception $e) {
            DB::rollBack();
            \Log::error('Transaction failed: '.$e->getMessage(), [
                'exception' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create transaction: '.$e->getMessage()
            ], 500);
        }
    }
    
    public function show($id)
    {
        $transaction = Transaction::with([
            'customer',
            'customer.village:id,name',
            'customer.subdistrict:id,name',
            'customer.city:id,name',
            'customer.province:id,name',
            'seller:id,name',
            'seller.vehicle:id,name,seller_id',
            'items.productVariant.product',
            'installments',
            'outstanding'
        ])->findOrFail($id);

        return view('transactions.show', compact('transaction'));
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $transaction = Transaction::findOrFail($id);
            
            // Delete related records
            $transaction->items()->delete();
            $transaction->installments()->delete();
            $transaction->outstanding()->delete();
            
            // Delete the transaction
            $transaction->delete();

            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete transaction: '.$e->getMessage());
        }
    }

    // Add this method to TransactionController
    public function payInstallment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'payment_amount' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $transaction = Transaction::findOrFail($id);
            
            // Check if payment amount is valid
            $outstanding = $transaction->outstanding;
            if ($request->payment_amount > $outstanding->outstanding_amount) {
                throw new Exception("Payment amount cannot be greater than outstanding amount");
            }

            // Update outstanding amount
            $outstanding->outstanding_amount -= $request->payment_amount;
            $outstanding->save();


            // Create installment payment record
            $transaction->installments()->create([
                'installment_amount' => $request->payment_amount,
                'payment_date' => $request->payment_date,
            ]);

            // Update transaction status if fully paid
            if ($outstanding->outstanding_amount == 0) {
                $transaction->status = 'paid';
                $transaction->save();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Installment payment recorded successfully');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to record payment: '.$e->getMessage());
        }
    }
}