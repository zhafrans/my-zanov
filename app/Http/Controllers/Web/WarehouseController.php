<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    public function index(Request $request)
    {
        $warehouses = Warehouse::when($request->search, function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->type, function($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query());

        return view('warehouses.index', compact('warehouses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:warehouses',
            'name' => 'required|string|max:100',
            'type' => 'required|in:WAREHOUSE,CAR',
        ]);

        DB::beginTransaction();

        try {
            Warehouse::create([
                'code' => $request->code,
                'name' => $request->name,
                'type' => $request->type,
            ]);

            DB::commit();
            return redirect()->route('warehouses.index')->with('success', 'Warehouse created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create warehouse: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:warehouses,code,'.$id,
            'name' => 'required|string|max:100',
            'type' => 'required|in:WAREHOUSE,CAR',
        ]);

        $warehouse = Warehouse::findOrFail($id);

        DB::beginTransaction();

        try {
            $warehouse->update([
                'code' => $request->code,
                'name' => $request->name,
                'type' => $request->type,
            ]);

            DB::commit();
            return redirect()->route('warehouses.index')->with('success', 'Warehouse updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update warehouse: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        return view('warehouses.show', compact('warehouse'));
    }
    
    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $warehouse = Warehouse::findOrFail($id);
            $warehouse->delete();
            
            DB::commit();
            return redirect()->route('warehouses.index')->with('success', 'Warehouse deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete warehouse: '.$e->getMessage());
        }
    }
}