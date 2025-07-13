<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\StockAmount;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAmountController extends Controller
{
    public function index(Request $request)
    {
        $warehouses = Warehouse::with(['stockAmounts' => function($query) use ($request) {
            if ($request->search) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }
        }])
        ->when($request->warehouse_id, function($query) use ($request) {
            $query->where('id', $request->warehouse_id);
        })
        ->orderBy('name')
        ->paginate(10)
        ->appends($request->query());

        $allWarehouses = Warehouse::orderBy('name')->get(['id', 'name']);

        return view('stock-amounts.index', compact('warehouses', 'allWarehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'warehouse_id' => 'required|exists:warehouses,id',
            'amount' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            StockAmount::create([
                'name' => $request->name,
                'warehouse_id' => $request->warehouse_id,
                'amount' => $request->amount,
            ]);

            DB::commit();
            return redirect()->route('stock-amounts.index')->with('success', 'Stock Amount created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create stock amount: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'warehouse_id' => 'required|exists:warehouses,id',
            'amount' => 'required|integer|min:0',
        ]);

        $stockAmount = StockAmount::findOrFail($id);

        DB::beginTransaction();

        try {
            $stockAmount->update([
                'name' => $request->name,
                'warehouse_id' => $request->warehouse_id,
                'amount' => $request->amount,
            ]);

            DB::commit();
            return redirect()->route('stock-amounts.index')->with('success', 'Stock Amount updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update stock amount: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $stockAmount = StockAmount::with(['warehouse'])->findOrFail($id);
        return view('stock-amounts.show', compact('stockAmount'));
    }
    
    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $stockAmount = StockAmount::findOrFail($id);
            $stockAmount->delete();
            
            DB::commit();
            return redirect()->route('stock-amounts.index')->with('success', 'Stock Amount deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete stock amount: '.$e->getMessage());
        }
    }

    public function getWarehouseStock(Warehouse $warehouse)
    {
        $warehouse->load('stockAmounts');
        return response()->json($warehouse);
    }
}