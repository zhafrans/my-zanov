<?php

namespace Database\Seeders;

use App\Models\Color;
use App\Models\Heel;
use App\Models\Product;
use App\Models\Size;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class ProductVariantSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::all();
        $colors = Color::all();
        $sizes = Size::all();
        $heels = Heel::all();

        if ($products->isEmpty() || $colors->isEmpty() || $sizes->isEmpty() || $heels->isEmpty()) {
            $this->command->warn('Pastikan products, colors, sizes, dan heels memiliki data.');
            return;
        }

        $now = Carbon::now();
        $yearMonth = $now->format('Ym');
        $baseSequence = 1;
        $codeCounters = [];

        $cleanCode = function ($string, $length) {
            return strtoupper(substr(str_replace(' ', '', trim($string)), 0, $length));
        };

        foreach (range(1, 30) as $i) {
            $product = $products->random();
            $color   = $colors->random();
            $size    = $sizes->random();
            $heel    = $heels->random();

            $productCode = $cleanCode($product->name, 1);
            $colorCode   = $cleanCode($color->name, 3);
            $heelCode    = strtoupper($heel->code);
            $sizeVal     = preg_replace('/\s+/', '', $size->value);

            $baseCode = $productCode . $sizeVal . $heelCode . $colorCode . $yearMonth . str_pad($baseSequence, 3, '0', STR_PAD_LEFT);
            $baseSequence++;

            $codeKey = $productCode . $heelCode;
            if (!isset($codeCounters[$codeKey])) {
                $codeCounters[$codeKey] = 1;
            }

            $shortCode = $productCode . $heelCode . str_pad($codeCounters[$codeKey], 2, '0', STR_PAD_LEFT);
            $codeCounters[$codeKey]++;

            // Harga acak
            $price = rand(150000, 200000);
            $installmentPrice = rand(250000, 300000);

            ProductVariant::create([
                'base_code'          => $baseCode,
                'code'               => $shortCode,
                'product_id'         => $product->id,
                'color_id'           => $color->id,
                'size_id'            => $size->id,
                'heel_id'            => $heel->id,
                'image'              => 'default.jpg',
                'price'              => $price,
                'installment_price'  => $installmentPrice,
            ]);
        }
    }
}
