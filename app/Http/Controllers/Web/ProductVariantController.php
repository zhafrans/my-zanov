<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Heel;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Size;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductVariantController extends Controller
{
    public function index(Request $request)
    {
        $variants = ProductVariant::with(['product', 'color', 'size', 'heel'])
            ->when($request->search && $request->search_type == 'code', function($query) use ($request) {
                $query->where('code', 'like', '%' . $request->search . '%');
            })
            ->when($request->search && $request->search_type == 'base_code', function($query) use ($request) {
                $query->where('base_code', 'like', '%' . $request->search . '%');
            })
            ->when($request->product_id, function($query) use ($request) {
                $query->where('product_id', $request->product_id);
            })
            ->when($request->color_id, function($query) use ($request) {
                $query->where('color_id', $request->color_id);
            })
            ->when($request->size_id, function($query) use ($request) {
                $query->where('size_id', $request->size_id);
            })
            ->when($request->heel_id, function($query) use ($request) {
                $query->where('heel_id', $request->heel_id);
            })
            ->when($request->sort_by && $request->sort_direction, function($query) use ($request) {
                $query->orderBy($request->sort_by, $request->sort_direction);
            }, function($query) {
                $query->orderByDesc('id');
            })
            ->paginate(10)
            ->appends($request->query());

        $products = Product::orderBy('name')->get(['id', 'name']);
        $colors = Color::orderBy('name')->get(['id', 'name']);
        $sizes = Size::orderBy('name')->get(['id', 'name']);
        $heels = Heel::orderBy('name')->get(['id', 'name']);

        return view('product-variants.index', compact('variants', 'products', 'colors', 'sizes', 'heels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'color_id' => 'required|exists:colors,id',
            'size_id' => 'required|exists:sizes,id',
            'heel_id' => 'required|exists:heels,id',
            'price' => 'required|numeric|min:0',
            'installment_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($request->product_id);
            $color = Color::findOrFail($request->color_id);
            $size = Size::findOrFail($request->size_id);
            $heel = Heel::findOrFail($request->heel_id);

            $now = now();
            $yearMonth = $now->format('Ym');
            $baseSequence = ProductVariant::whereYear('created_at', $now->year)
                                ->whereMonth('created_at', $now->month)
                                ->count() + 1;

            $cleanCode = function ($string, $length) {
                return strtoupper(substr(str_replace(' ', '', trim($string)), 0, $length));
            };

            $productCode = $cleanCode($product->name, 1);
            $colorCode = $cleanCode($color->name, 3);
            $heelCode = strtoupper($heel->code);
            $sizeVal = preg_replace('/\s+/', '', $size->name);

            $baseCode = $productCode . $sizeVal . $heelCode . $colorCode . $yearMonth . str_pad($baseSequence, 3, '0', STR_PAD_LEFT);

            $codeKey = $productCode . $heelCode;
            $codeCount = ProductVariant::where('code', 'like', $productCode . $heelCode . '%')->count() + 1;
            $shortCode = $productCode . $heelCode . str_pad($codeCount, 2, '0', STR_PAD_LEFT);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = Str::random(20) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('product-variants', $filename, 'public');
            }

            ProductVariant::create([
                'base_code' => $baseCode,
                'code' => $shortCode,
                'product_id' => $product->id,
                'color_id' => $color->id,
                'size_id' => $size->id,
                'heel_id' => $heel->id,
                'image' => $imagePath ?? 'default.jpg',
                'price' => $request->price,
                'installment_price' => $request->installment_price,
            ]);

            DB::commit();
            return redirect()->route('product-variants.index')->with('success', 'Product variant created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create product variant: '.$e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'price' => 'required|numeric|min:0',
            'installment_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $variant = ProductVariant::findOrFail($id);

        DB::beginTransaction();

        try {
            $imagePath = $variant->image;
            if ($request->hasFile('image')) {
                // Delete old image if not default
                if ($variant->image !== 'default.jpg' && Storage::disk('public')->exists($variant->image)) {
                    Storage::disk('public')->delete($variant->image);
                }
                
                $image = $request->file('image');
                $filename = Str::random(20) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('product-variants', $filename, 'public');
            }

            $variant->update([
                'image' => $imagePath,
                'price' => $request->price,
                'installment_price' => $request->installment_price,
            ]);

            DB::commit();
            return redirect()->route('product-variants.index')->with('success', 'Product variant updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update product variant: '.$e->getMessage());
        }
    }

    public function show($id)
    {
        $variant = ProductVariant::with(['product', 'color', 'size', 'heel'])->findOrFail($id);
        return view('product-variants.show', compact('variant'));
    }
    
    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $variant = ProductVariant::findOrFail($id);
            
            // Delete image if not default
            if ($variant->image !== 'default.jpg' && Storage::disk('public')->exists($variant->image)) {
                Storage::disk('public')->delete($variant->image);
            }
            
            $variant->delete();
            
            DB::commit();
            return redirect()->route('product-variants.index')->with('success', 'Product variant deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete product variant: '.$e->getMessage());
        }
    }
}