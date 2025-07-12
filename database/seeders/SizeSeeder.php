<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SizeSeeder extends Seeder
{
    public function run(): void
    {
        for ($size = 36; $size <= 43; $size++) {
            DB::table('sizes')->insert([
                'code' => 'SZ' . $size,
                'name' => (string)$size,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
