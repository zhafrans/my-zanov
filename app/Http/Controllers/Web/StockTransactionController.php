<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\StockTransaction;
use App\Models\StockAmount;
use App\Models\User;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = StockTransaction::with(['user', 'warehouse', 'toWarehouse', 'stockAmount.warehouse'])
            ->when($request->filled('search'), function($query) use ($request) {
                $query->where('note', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('warehouse_id'), function($query) use ($request) {
                $query->where('warehouse_id', $request->warehouse_id);
            })
            ->when($request->filled('to_warehouse_id'), function($query) use ($request) {
                $query->where('to_warehouse_id', $request->to_warehouse_id);
            })
            ->when($request->filled('destination'), function($query) use ($request) {
                $query->where('destination', $request->destination);
            })
            ->when($request->filled('type'), function($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when($request->has('is_adjustment') && $request->is_adjustment !== '', function($query) use ($request) {
                $query->where('is_adjustment', $request->is_adjustment);
            })
            ->when($request->filled('user_id'), function($query) use ($request) { // Tambahkan filter user
                $query->where('user_id', $request->user_id);
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->appends($request->except('page'));

        $warehouses = Warehouse::orderBy('name')->get(['id', 'name']);
        $users = User::orderBy('name')->get(['id', 'name']);
        $destinations = [
            'lost' => 'Lost',
            'transfer' => 'Transfer',
            'add' => 'Add'
        ];
        
        $types = [
            'in' => 'In',
            'out' => 'Out'
        ];

        $stockAmounts = StockAmount::with('warehouse')->get();

        return view('stock-transactions.index', compact('transactions', 'warehouses', 'users', 'destinations', 'types', 'stockAmounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'stock_amount_id' => 'required|exists:stock_amounts,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:in,out',
            'is_adjustment' => 'sometimes|boolean',
            'to_warehouse_id' => 'nullable|required_if:destination,transfer|exists:warehouses,id',
            'destination' => 'nullable|in:lost,transfer,add',
            'note' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Dapatkan stock amount yang terkait
            $stockAmount = StockAmount::findOrFail($request->stock_amount_id);
            
            // Hitung jumlah stok sebelum transaksi
            $quantityBefore = $stockAmount->total_amount;
            
            // Buat transaksi
            $transaction = StockTransaction::create([
                'stock_amount_id' => $request->stock_amount_id,
                'warehouse_id' => $request->warehouse_id,
                'quantity' => $request->quantity,
                'type' => $request->type,
                'is_adjustment' => $request->is_adjustment ?? false,
                'to_warehouse_id' => $request->to_warehouse_id,
                'destination' => $request->destination,
                'note' => $request->note,
                'quantity_before' => $quantityBefore, // Simpan jumlah sebelum transaksi
                'user_id' => Auth::id(),
            ]);

            // Update stock amount berdasarkan jenis transaksi
            if ($request->type === 'in') {
                $stockAmount->total_amount += $request->quantity;
            } else { // type 'out'
                // Validasi stok cukup
                if ($stockAmount->total_amount < $request->quantity && !$request->is_adjustment) {
                    throw new Exception('Insufficient stock for this transaction');
                }
                
                $stockAmount->total_amount -= $request->quantity;
            }
            
            // Simpan perubahan stock amount
            $stockAmount->save();
            
            // Update quantity_after pada transaksi
            $transaction->update(['quantity_after' => $stockAmount->total_amount]);

            // Jika transfer, update gudang tujuan
            if ($request->destination === 'transfer' && $request->to_warehouse_id) {
                $toStockAmount = StockAmount::where('warehouse_id', $request->to_warehouse_id)
                    ->firstOrFail();
                
                $toStockAmount->total_amount += $request->quantity;
                $toStockAmount->save();
            }

            DB::commit();
            return redirect()->route('stock-transactions.index')->with('success', 'Stock transaction created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create stock transaction: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'stock_amount_id' => 'required|exists:stock_amounts,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:in,out',
            'is_adjustment' => 'sometimes|boolean',
            'to_warehouse_id' => 'nullable|required_if:destination,transfer|exists:warehouses,id',
            'destination' => 'nullable|in:lost,transfer,add',
            'note' => 'nullable|string|max:500',
        ]);

        $transaction = StockTransaction::findOrFail($id);

        DB::beginTransaction();

        try {
            // Dapatkan stock amount yang terkait
            $stockAmount = StockAmount::findOrFail($request->stock_amount_id);
            
            // Kembalikan stok ke keadaan sebelum transaksi
            if ($transaction->type === 'in') {
                $stockAmount->total_amount -= $transaction->quantity;
            } else { // type 'out'
                $stockAmount->total_amount += $transaction->quantity;
            }
            
            // Jika transfer sebelumnya, kembalikan stok gudang tujuan
            if ($transaction->destination === 'transfer' && $transaction->to_warehouse_id) {
                $toStockAmount = StockAmount::where('warehouse_id', $transaction->to_warehouse_id)
                    ->firstOrFail();
                
                $toStockAmount->total_amount -= $transaction->quantity;
                $toStockAmount->save();
            }
            
            // Simpan quantity_before untuk update
            $quantityBefore = $stockAmount->total_amount;
            
            // Update transaksi
            $transaction->update([
                'stock_amount_id' => $request->stock_amount_id,
                'warehouse_id' => $request->warehouse_id,
                'quantity' => $request->quantity,
                'type' => $request->type,
                'is_adjustment' => $request->is_adjustment ?? false,
                'to_warehouse_id' => $request->to_warehouse_id,
                'destination' => $request->destination,
                'note' => $request->note,
                'quantity_before' => $quantityBefore,
            ]);

            // Update stock amount berdasarkan jenis transaksi baru
            if ($request->type === 'in') {
                $stockAmount->total_amount += $request->quantity;
            } else { // type 'out'
                // Validasi stok cukup
                if ($stockAmount->total_amount < $request->quantity && !$request->is_adjustment) {
                    throw new Exception('Insufficient stock for this transaction');
                }
                
                $stockAmount->total_amount -= $request->quantity;
            }
            
            // Simpan perubahan stock amount
            $stockAmount->save();
            
            // Update quantity_after pada transaksi
            $transaction->update(['quantity_after' => $stockAmount->total_amount]);

            // Jika transfer baru, update gudang tujuan
            if ($request->destination === 'transfer' && $request->to_warehouse_id) {
                $toStockAmount = StockAmount::where('warehouse_id', $request->to_warehouse_id)
                    ->firstOrFail();
                
                $toStockAmount->total_amount += $request->quantity;
                $toStockAmount->save();
            }

            DB::commit();
            return redirect()->route('stock-transactions.index')->with('success', 'Stock transaction updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update stock transaction: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $transaction = StockTransaction::with(['warehouse', 'toWarehouse', 'stockAmount'])->findOrFail($id);
        return view('stock-transactions.show', compact('transaction'));
    }
    
    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $transaction = StockTransaction::findOrFail($id);
            $stockAmount = $transaction->stockAmount;
            
            // Kembalikan stok ke keadaan sebelum transaksi
            if ($transaction->type === 'in') {
                $stockAmount->total_amount -= $transaction->quantity;
            } else { // type 'out'
                $stockAmount->total_amount += $transaction->quantity;
            }
            
            // Jika transfer, kembalikan stok gudang tujuan
            if ($transaction->destination === 'transfer' && $transaction->to_warehouse_id) {
                $toStockAmount = StockAmount::where('warehouse_id', $transaction->to_warehouse_id)
                    ->firstOrFail();
                
                $toStockAmount->total_amount -= $transaction->quantity;
                $toStockAmount->save();
            }
            
            // Simpan perubahan stock amount
            $stockAmount->save();
            
            // Hapus transaksi
            $transaction->delete();
            
            DB::commit();
            return redirect()->route('stock-transactions.index')->with('success', 'Stock transaction deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete stock transaction: '.$e->getMessage());
        }
    }
}