<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Vehicle;
use App\Models\Warehouse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $vehicles = Vehicle::with(['warehouse:id,name', 'driver:id,name', 'seller:id,name'])
            ->when($request->search && $request->search_type == 'name', function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->search && $request->search_type == 'licence_plate', function($query) use ($request) {
                $query->where('licence_plate', 'like', '%' . $request->search . '%');
            })
            ->when($request->driver_id, function($query) use ($request) {
                $query->where('driver_id', $request->driver_id);
            })
            ->when($request->seller_id, function($query) use ($request) {
                $query->where('seller_id', $request->seller_id);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query());

        $warehouses = Warehouse::orderBy('name')->get(['id', 'name']);
        
        // Get DRIVER role ID
        $driverRole = UserRole::where('code', 'DRIVER')->first();
        $drivers = User::where('role_id', $driverRole->id)
                    ->orderBy('name')
                    ->get(['id', 'name']);
        
        // Get SELLER role ID
        $sellerRole = UserRole::where('code', 'SALES')->first();
        $sellers = User::where('role_id', $sellerRole->id)
                    ->orderBy('name')
                    ->get(['id', 'name']);

        return view('vehicles.index', compact('vehicles', 'warehouses', 'drivers', 'sellers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:vehicles',
            'name' => 'required|string|max:100',
            'licence_plate' => 'required|string|max:20|unique:vehicles',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'driver_id' => 'nullable|exists:users,id',
            'seller_id' => 'nullable|exists:users,id',
        ]);

        DB::beginTransaction();

        try {
            Vehicle::create([
                'code' => $request->code,
                'name' => $request->name,
                'licence_plate' => $request->licence_plate,
                'warehouse_id' => $request->warehouse_id,
                'driver_id' => $request->driver_id,
                'seller_id' => $request->seller_id,
            ]);

            DB::commit();
            return redirect()->route('vehicles.index')->with('success', 'Vehicle created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create vehicle: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:vehicles,code,'.$id,
            'name' => 'required|string|max:100',
            'licence_plate' => 'required|string|max:20|unique:vehicles,licence_plate,'.$id,
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'driver_id' => 'nullable|exists:users,id',
            'seller_id' => 'nullable|exists:users,id',
        ]);

        $vehicle = Vehicle::findOrFail($id);

        DB::beginTransaction();

        try {
            $vehicle->update([
                'code' => $request->code,
                'name' => $request->name,
                'licence_plate' => $request->licence_plate,
                'warehouse_id' => $request->warehouse_id,
                'driver_id' => $request->driver_id,
                'seller_id' => $request->seller_id,
            ]);

            DB::commit();
            return redirect()->route('vehicles.index')->with('success', 'Vehicle updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update vehicle: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $vehicle = Vehicle::with(['warehouse', 'driver', 'seller'])->findOrFail($id);
        return view('vehicles.show', compact('vehicle'));
    }
    
    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $vehicle = Vehicle::findOrFail($id);
            $vehicle->delete();
            
            DB::commit();
            return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete vehicle: '.$e->getMessage());
        }
    }
}