<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::when($request->search && $request->search_type == 'name', function($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->when($request->search && $request->search_type == 'code', function($query) use ($request) {
                $query->where('code', 'like', '%' . $request->search . '%');
            })
            ->when($request->gender, function($query) use ($request) {
                $query->where('gender', $request->gender);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query());

        return view('products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:products',
            'name' => 'required|string|max:100',
            'gender' => 'required|in:man,woman,unisex',
        ]);

        DB::beginTransaction();

        try {
            Product::create([
                'code' => $request->code,
                'name' => $request->name,
                'gender' => $request->gender,
            ]);

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Product created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create product: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:products,code,'.$id,
            'name' => 'required|string|max:100',
            'gender' => 'required|in:man,woman,unisex',
        ]);

        $product = Product::findOrFail($id);

        DB::beginTransaction();

        try {
            $product->update([
                'code' => $request->code,
                'name' => $request->name,
                'gender' => $request->gender,
            ]);

            DB::commit();
            return redirect()->route('products.index')->with('success', 'Product updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update product: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('products.show', compact('product'));
    }
    
    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            
            DB::commit();
            return redirect()->route('products.index')->with('success', 'Product deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete product: '.$e->getMessage());
        }
    }
}