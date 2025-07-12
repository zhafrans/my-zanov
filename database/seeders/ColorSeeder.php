<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = ['RED', 'WHITE', 'BLACK', 'BLUE', 'BROWN', 'GREY'];

        foreach ($colors as $color) {
            DB::table('colors')->insert([
                'code' => strtoupper(Str::slug($color, '')), // contoh: RED, WHITE
                'name' => ucfirst(strtolower($color)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
