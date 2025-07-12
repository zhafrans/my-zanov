<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Heel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HeelController extends Controller
{
    public function index(Request $request)
    {
        $heels = Heel::when($request->search && $request->search_type == 'name', function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->search && $request->search_type == 'code', function($query) use ($request) {
                $query->where('code', 'like', '%' . $request->search . '%');
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query());

        return view('heels.index', compact('heels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:heels',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            Heel::create([
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
            ]);

            DB::commit();
            return redirect()->route('heels.index')->with('success', 'Heel created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create heel: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:heels,code,'.$id,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        $heel = Heel::findOrFail($id);

        DB::beginTransaction();

        try {
            $heel->update([
                'code' => $request->code,
                'name' => $request->name,
                'description' => $request->description,
            ]);

            DB::commit();
            return redirect()->route('heels.index')->with('success', 'Heel updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update heel: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $heel = Heel::findOrFail($id);
        return view('heels.show', compact('heel'));
    }
    
    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $heel = Heel::findOrFail($id);
            $heel->delete();
            
            DB::commit();
            return redirect()->route('heels.index')->with('success', 'Heel deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete heel: '.$e->getMessage());
        }
    }
}