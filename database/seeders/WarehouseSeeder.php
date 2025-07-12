<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = [
            [
                'name' => 'ZANOV 01',
                'type' => 'CAR',
            ],
            [
                'name' => 'ZANOV 02',
                'type' => 'CAR',
            ],
            [
                'name' => 'ZANOV 03',
                'type' => 'CAR',
            ],
            [
                'name' => 'ZANOV 04',
                'type' => 'CAR',
            ],
            [
                'name' => 'Warehouse Teluk',
                'type' => 'WAREHOUSE',
            ],
            [
                'name' => 'Warehouse Pliken',
                'type' => 'WAREHOUSE',
            ],
        ];

        foreach ($warehouses as $warehouse) {
            DB::table('warehouses')->insert([
                'code' => 'WH-' . fake()->numerify('###'),
                'name' => $warehouse['name'],
                'type' => $warehouse['type'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
