<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // ['code' => 'W', 'name' => 'Wedges'],
            // ['code' => 'F', 'name' => 'Flat'],
            // ['code' => 'T', 'name' => 'Tahu'],
            // ['code' => 'R', 'name' => 'Rajut'],
            // ['code' => 'B', 'name' => 'B'],
            // ['code' => 'Z006', 'name' => 'Zanov Cloud X'],
            // ['code' => 'Z007', 'name' => 'Zanov Urban Pro'],
            // ['code' => 'Z008', 'name' => 'Zanov Urban Lite'],
            // ['code' => 'Z009', 'name' => 'Zanov Flexi Max'],
            // ['code' => 'Z010', 'name' => 'Zanov Boost Drive'],
            // ['code' => 'Z011', 'name' => 'Zanov Retro Vibe'],
            // ['code' => 'Z012', 'name' => 'Zanov Sky High'],
            // ['code' => 'Z013', 'name' => 'Zanov Active Core'],
            // ['code' => 'Z014', 'name' => 'Zanov Active Lite'],
            // ['code' => 'Z015', 'name' => 'Zanov Zoom Edge'],
            // ['code' => 'Z016', 'name' => 'Zanov Dash Pro'],
            // ['code' => 'Z017', 'name' => 'Zanov Dash Soft'],
            // ['code' => 'Z018', 'name' => 'Zanov Glide Boost'],
            // ['code' => 'Z019', 'name' => 'Zanov Pulse Fit'],
            // ['code' => 'Z020', 'name' => 'Zanov Wave Rider'],
        ];

        DB::table('products')->insert($products);
    }
}
