<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SizeController extends Controller
{
    public function index(Request $request)
    {
        $sizes = Size::when($request->search && $request->search_type == 'name', function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->search && $request->search_type == 'code', function($query) use ($request) {
                $query->where('code', 'like', '%' . $request->search . '%');
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query());

        return view('sizes.index', compact('sizes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:sizes',
            'name' => 'required|string|max:100',
        ]);

        DB::beginTransaction();

        try {
            Size::create([
                'code' => $request->code,
                'name' => $request->name,
            ]);

            DB::commit();
            return redirect()->route('sizes.index')->with('success', 'Size created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create size: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:sizes,code,'.$id,
            'name' => 'required|string|max:100',
        ]);

        $size = Size::findOrFail($id);

        DB::beginTransaction();

        try {
            $size->update([
                'code' => $request->code,
                'name' => $request->name,
            ]);

            DB::commit();
            return redirect()->route('sizes.index')->with('success', 'Size updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update size: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $size = Size::findOrFail($id);
        return view('sizes.show', compact('size'));
    }
    
    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $size = Size::findOrFail($id);
            $size->delete();
            
            DB::commit();
            return redirect()->route('sizes.index')->with('success', 'Size deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete size: '.$e->getMessage());
        }
    }
}