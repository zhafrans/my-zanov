<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\StockAmount;
use App\Models\StockAmountItem;
use App\Models\StockType;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAmountController extends Controller
{
    public function index(Request $request)
    {
        $stockAmounts = StockAmount::with(['warehouse', 'items.stockType'])
            ->when($request->search, function($query) use ($request) {
                $query->whereHas('items', function($q) use ($request) {
                    $q->where('amount', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->warehouse_id, function($query) use ($request) {
                $query->where('warehouse_id', $request->warehouse_id);
            })
            ->when($request->stock_type_id, function($query) use ($request) {
                $query->whereHas('items', function($q) use ($request) {
                    $q->where('stock_type_id', $request->stock_type_id);
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query());

        $warehouses = Warehouse::orderBy('name')->get(['id', 'name']);
        $stockTypes = StockType::orderBy('name')->get(['id', 'name']);

        return view('stock-amounts.index', compact('stockAmounts', 'warehouses', 'stockTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|array',
            'items.*.stock_type_id' => 'required|exists:stock_types,id',
            'items.*.amount' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            $stockAmount = StockAmount::create([
                'warehouse_id' => $request->warehouse_id,
            ]);

            foreach ($request->items as $item) {
                StockAmountItem::create([
                    'stock_amount_id' => $stockAmount->id,
                    'stock_type_id' => $item['stock_type_id'],
                    'amount' => $item['amount'],
                ]);
            }

            // Update total amount
            $stockAmount->update([
                'total_amount' => $stockAmount->items()->sum('amount')
            ]);

            DB::commit();
            return redirect()->route('stock-amounts.index')->with('success', 'Stock amount created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create stock amount: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'items' => 'required|array',
            'items.*.stock_type_id' => 'required|exists:stock_types,id',
            'items.*.amount' => 'required|integer|min:0',
        ]);

        $stockAmount = StockAmount::findOrFail($id);

        DB::beginTransaction();

        try {
            $stockAmount->update([
                'warehouse_id' => $request->warehouse_id,
            ]);

            // Delete existing items
            $stockAmount->items()->delete();

            // Create new items
            foreach ($request->items as $item) {
                StockAmountItem::create([
                    'stock_amount_id' => $stockAmount->id,
                    'stock_type_id' => $item['stock_type_id'],
                    'amount' => $item['amount'],
                ]);
            }

            // Update total amount
            $stockAmount->update([
                'total_amount' => $stockAmount->items()->sum('amount')
            ]);

            DB::commit();
            return redirect()->route('stock-amounts.index')->with('success', 'Stock amount updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update stock amount: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $stockAmount = StockAmount::with(['warehouse', 'items.stockType'])->findOrFail($id);
        return view('stock-amounts.show', compact('stockAmount'));
    }
    
    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $stockAmount = StockAmount::findOrFail($id);
            $stockAmount->items()->delete();
            $stockAmount->delete();
            
            DB::commit();
            return redirect()->route('stock-amounts.index')->with('success', 'Stock amount deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete stock amount: '.$e->getMessage());
        }
    }
}