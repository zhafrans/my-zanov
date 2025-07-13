<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockAmount;
use App\Models\Warehouse;

class StockAmountSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = Warehouse::all(); // Tanpa filter type

        if ($warehouses->isEmpty()) {
            $this->command->warn('Tidak ada data warehouse.');
            return;
        }

        $stockNames = ['Sepatu Pria', 'Sepatu Wanita', 'Rajut'];

        foreach ($warehouses as $warehouse) {
            foreach ($stockNames as $name) {
                StockAmount::create([
                    'name' => $name,
                    'warehouse_id' => $warehouse->id,
                    'amount' => rand(200, 300),
                ]);
            }
        }

        $this->command->info('StockAmountSeeder berhasil dijalankan untuk semua warehouse.');
    }
}
