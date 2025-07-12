<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Province;
use App\Models\City;
use App\Models\Subdistrict;
use App\Models\Village;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with(['province', 'city', 'subdistrict', 'village'])
            ->when($request->search, function($q) use ($request) {
                $searchType = $request->search_type ?? 'name';
                return $q->where($searchType, 'like', '%'.$request->search.'%');
            })
            ->when($request->province_id, function($q) use ($request) {
                return $q->where('province_id', $request->province_id);
            })
            ->when($request->city_id, function($q) use ($request) {
                return $q->where('city_id', $request->city_id);
            })
            ->when($request->subdistrict_id, function($q) use ($request) {
                return $q->where('subdistrict_id', $request->subdistrict_id);
            })
            ->when($request->village_id, function($q) use ($request) {
                return $q->where('village_id', $request->village_id);
            })
            ->orderBy('created_at', 'desc'); 

        $customers = $query->paginate(10);
        $provinces = Province::orderBy('name')->get();
        
        // Load locations based on current filters
        $cities = $request->province_id 
            ? City::where('province_id', $request->province_id)->get() 
            : collect();
        
        $subdistricts = $request->city_id 
            ? Subdistrict::where('city_id', $request->city_id)->get() 
            : collect();
        
        $villages = $request->subdistrict_id 
            ? Village::where('subdistrict_id', $request->subdistrict_id)->get() 
            : collect();

        return view('customers.index', compact(
            'customers', 'provinces', 'cities', 'subdistricts', 'villages'
        ));
    }

    public function create()
    {
        $provinces = Province::orderBy('name')->get();
        
        // Ambil old values jika ada
        $cities = old('province_id') ? City::where('province_id', old('province_id'))->get() : collect();
        $subdistricts = old('city_id') ? Subdistrict::where('city_id', old('city_id'))->get() : collect();
        $villages = old('subdistrict_id') ? Village::where('subdistrict_id', old('subdistrict_id'))->get() : collect();

        return view('customers.create', compact('provinces', 'cities', 'subdistricts', 'villages'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:customers',
            'name' => 'required|string|max:100',
            'address' => 'required|string',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'subdistrict_id' => 'required|exists:subdistricts,id',
            'village_id' => 'required|exists:villages,id',
            'phone' => 'nullable|string|max:20',
        ]);

        DB::beginTransaction();

        try {
            Customer::create($request->only([
                'code', 'name', 'address', 'province_id', 'city_id',
                'subdistrict_id', 'village_id', 'phone'
            ]));

            DB::commit();
            return redirect()->route('customers.index')
                ->with('success', 'Customer created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to create customer: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $customer = Customer::with(['province', 'city', 'subdistrict', 'village'])
            ->findOrFail($id);
        return view('customers.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $provinces = Province::orderBy('name')->get();
        $cities = City::where('province_id', $customer->province_id)->get();
        $subdistricts = Subdistrict::where('city_id', $customer->city_id)->get();
        $villages = Village::where('subdistrict_id', $customer->subdistrict_id)->get();

        return view('customers.edit', compact(
            'customer', 'provinces', 'cities', 'subdistricts', 'villages'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:customers,code,'.$id,
            'name' => 'required|string|max:100',
            'address' => 'required|string',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'subdistrict_id' => 'required|exists:subdistricts,id',
            'village_id' => 'required|exists:villages,id',
            'phone' => 'nullable|string|max:20',
        ]);

        $customer = Customer::findOrFail($id);

        DB::beginTransaction();

        try {
            $customer->update($request->only([
                'code', 'name', 'address', 'province_id', 'city_id',
                'subdistrict_id', 'village_id', 'phone'
            ]));

            DB::commit();
            return redirect()->route('customers.index')
                ->with('success', 'Customer updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Failed to update customer: '.$e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();
            
            DB::commit();
            return redirect()->route('customers.index')
                ->with('success', 'Customer deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Failed to delete customer: '.$e->getMessage());
        }
    }
}