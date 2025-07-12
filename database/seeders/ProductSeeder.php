<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['code' => 'Z001', 'name' => 'Zanov Runner Alpha', 'gender' => 'man'],
            ['code' => 'Z002', 'name' => 'Zanov Runner Beta', 'gender' => 'woman'],
            ['code' => 'Z003', 'name' => 'Zanov Air Flow', 'gender' => 'unisex'],
            ['code' => 'Z004', 'name' => 'Zanov Street One', 'gender' => 'man'],
            ['code' => 'Z005', 'name' => 'Zanov Street Two', 'gender' => 'unisex'],
            ['code' => 'Z006', 'name' => 'Zanov Cloud X', 'gender' => 'woman'],
            ['code' => 'Z007', 'name' => 'Zanov Urban Pro', 'gender' => 'unisex'],
            ['code' => 'Z008', 'name' => 'Zanov Urban Lite', 'gender' => 'man'],
            ['code' => 'Z009', 'name' => 'Zanov Flexi Max', 'gender' => 'woman'],
            ['code' => 'Z010', 'name' => 'Zanov Boost Drive', 'gender' => 'man'],
            ['code' => 'Z011', 'name' => 'Zanov Retro Vibe', 'gender' => 'woman'],
            ['code' => 'Z012', 'name' => 'Zanov Sky High', 'gender' => 'unisex'],
            ['code' => 'Z013', 'name' => 'Zanov Active Core', 'gender' => 'man'],
            ['code' => 'Z014', 'name' => 'Zanov Active Lite', 'gender' => 'woman'],
            ['code' => 'Z015', 'name' => 'Zanov Zoom Edge', 'gender' => 'unisex'],
            ['code' => 'Z016', 'name' => 'Zanov Dash Pro', 'gender' => 'man'],
            ['code' => 'Z017', 'name' => 'Zanov Dash Soft', 'gender' => 'woman'],
            ['code' => 'Z018', 'name' => 'Zanov Glide Boost', 'gender' => 'unisex'],
            ['code' => 'Z019', 'name' => 'Zanov Pulse Fit', 'gender' => 'man'],
            ['code' => 'Z020', 'name' => 'Zanov Wave Rider', 'gender' => 'unisex'],
        ];

        DB::table('products')->insert($products);
    }
}
