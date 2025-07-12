<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ColorController extends Controller
{
    public function index(Request $request)
    {
        $colors = Color::when($request->search && $request->search_type == 'name', function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->search && $request->search_type == 'code', function($query) use ($request) {
                $query->where('code', 'like', '%' . $request->search . '%');
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query());

        return view('colors.index', compact('colors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:colors',
            'name' => 'required|string|max:100',
        ]);

        DB::beginTransaction();

        try {
            Color::create([
                'code' => $request->code,
                'name' => $request->name,
            ]);

            DB::commit();
            return redirect()->route('colors.index')->with('success', 'Color created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create color: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:colors,code,'.$id,
            'name' => 'required|string|max:100',
        ]);

        $color = Color::findOrFail($id);

        DB::beginTransaction();

        try {
            $color->update([
                'code' => $request->code,
                'name' => $request->name,
            ]);

            DB::commit();
            return redirect()->route('colors.index')->with('success', 'Color updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update color: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $color = Color::findOrFail($id);
        return view('colors.show', compact('color'));
    }
    
    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $color = Color::findOrFail($id);
            $color->delete();
            
            DB::commit();
            return redirect()->route('colors.index')->with('success', 'Color deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete color: '.$e->getMessage());
        }
    }
}