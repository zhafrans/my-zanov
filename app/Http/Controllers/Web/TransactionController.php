<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::with(['customer:id,name', 'seller:id,name', 'productVariant:id,code,product_id', 'productVariant.product:id,name',])
            ->when($request->search, function($query) use ($request) {
                $query->where('invoice', 'like', '%' . $request->search . '%');
            })
            ->when($request->customer_id, function($query) use ($request) {
                $query->where('customer_id', $request->customer_id);
            })
            ->when($request->product_variant_id, function($query) use ($request) { // Changed from product_id
                $query->where('product_variant_id', $request->product_variant_id);
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
            ->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query());

        $customers = Customer::orderBy('name')->get(['id', 'name']);
        $productVariants = ProductVariant::with('product')->orderBy('code')->get(['id', 'code', 'product_id']); // Changed from products
        
        // Get SALES role users
        $sellerRole = DB::table('user_roles')->where('code', 'SALES')->first();
        $sellers = User::where('role_id', $sellerRole->id)
                    ->orderBy('name')
                    ->get(['id', 'name']);

        return view('transactions.index', compact(
            'transactions',
            'customers',
            'productVariants', // Changed from products
            'sellers'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'invoice' => 'required|string|max:50|unique:transactions',
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'seller_id' => 'required|exists:users,id',
            'payment_type' => 'required|in:credit,cash',
            'status' => 'required|in:paid,installment',
            'items' => 'required|array',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'installment_amount' => 'required_if:status,installment|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Create transaction
            $transaction = Transaction::create([
                'invoice' => $request->invoice,
                'customer_id' => $request->customer_id,
                'product_id' => $request->product_id,
                'seller_id' => $request->seller_id,
                'payment_type' => $request->payment_type,
                'status' => $request->status,
            ]);

            // Create transaction items
            foreach ($request->items as $item) {
                $variant = ProductVariant::find($item['product_variant_id']);
                
                $transaction->items()->create([
                    'product_variant_id' => $item['product_variant_id'],
                    'quantity' => $item['quantity'],
                    'snapshot_name' => $variant->name,
                    'snapshot_price' => $variant->price,
                ]);
            }

            // If installment, create installment record
            if ($request->status === 'installment') {
                $transaction->installments()->create([
                    'installment_amount' => $request->installment_amount,
                ]);

                $transaction->outstanding()->create([
                    'outstanding_amount' => $request->installment_amount,
                ]);
            }

            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaction created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create transaction: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'invoice' => 'required|string|max:50|unique:transactions,invoice,'.$id,
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'required|exists:products,id',
            'seller_id' => 'required|exists:users,id',
            'payment_type' => 'required|in:credit,cash',
            'status' => 'required|in:paid,installment',
            'installment_amount' => 'required_if:status,installment|numeric|min:0',
        ]);

        $transaction = Transaction::findOrFail($id);

        DB::beginTransaction();

        try {
            $transaction->update([
                'invoice' => $request->invoice,
                'customer_id' => $request->customer_id,
                'product_id' => $request->product_id,
                'seller_id' => $request->seller_id,
                'payment_type' => $request->payment_type,
                'status' => $request->status,
            ]);

            // Update installment if status is installment
            if ($request->status === 'installment') {
                if ($transaction->installments()->exists()) {
                    $transaction->installments()->update([
                        'installment_amount' => $request->installment_amount,
                    ]);
                } else {
                    $transaction->installments()->create([
                        'installment_amount' => $request->installment_amount,
                    ]);
                }

                if ($transaction->outstanding()->exists()) {
                    $transaction->outstanding()->update([
                        'outstanding_amount' => $request->installment_amount,
                    ]);
                } else {
                    $transaction->outstanding()->create([
                        'outstanding_amount' => $request->installment_amount,
                    ]);
                }
            } else {
                // If status changed to paid, delete installment and outstanding records
                $transaction->installments()->delete();
                $transaction->outstanding()->delete();
            }

            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update transaction: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $transaction = Transaction::with([
            'customer', 
            'seller', 
            'items.variant', 
            'installments',
            'outstanding',
            'productVariant.product'
        ])->findOrFail($id);
        
        return view('transactions.show', compact('transaction'));
    }
    
    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $transaction = Transaction::findOrFail($id);
            
            // Delete related records first
            $transaction->items()->delete();
            $transaction->installments()->delete();
            $transaction->outstanding()->delete();
            
            // Then delete the transaction
            $transaction->delete();
            
            DB::commit();
            return redirect()->route('transactions.index')->with('success', 'Transaction deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete transaction: '.$e->getMessage());
        }
    }
}