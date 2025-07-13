<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StockAmount;
use App\Models\Warehouse;
use App\Models\StockType;
use App\Models\StockAmountItem;

class StockAmountSeeder extends Seeder
{
    public function run(): void
    {
        $warehouses = Warehouse::all();
        $stockTypes = StockType::all();

        if ($warehouses->isEmpty()) {
            $this->command->warn('Tidak ada data warehouse.');
            return;
        }

        if ($stockTypes->isEmpty()) {
            $this->command->warn('Tidak ada data stock type.');
            return;
        }

        foreach ($warehouses as $warehouse) {
            // Buat 1 record stock_amount untuk warehouse ini
            $stockAmount = StockAmount::create([
                'warehouse_id' => $warehouse->id,
                'total_amount' => 0, // diupdate nanti setelah item diisi
            ]);

            $total = 0;

            foreach ($stockTypes as $type) {
                $amount = rand(200, 300);
                $total += $amount;

                StockAmountItem::create([
                    'stock_amount_id' => $stockAmount->id,
                    'stock_type_id' => $type->id,
                    'amount' => $amount,
                ]);
            }

            // Update total_amount setelah item selesai diinput
            $stockAmount->update(['total_amount' => $total]);
        }

        $this->command->info('StockAmountSeeder berhasil dijalankan dengan item berdasarkan stock_types.');
    }
}
