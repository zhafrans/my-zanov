<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockType;

class StockTypeSeeder extends Seeder
{
    public function run(): void
    {
        $stockTypes = [
            ['code' => 'SP', 'name' => 'Sepatu Pria'],
            ['code' => 'SW', 'name' => 'Sepatu Wanita'],
        ];

        foreach ($stockTypes as $type) {
            StockType::create($type);
        }
    }
}
