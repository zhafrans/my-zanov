<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\StockAmount;
use App\Models\Warehouse;
use App\Models\StockTransaction;
use Illuminate\Database\Seeder;

class StockTransactionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $stockAmounts = StockAmount::all();
        $warehouses = Warehouse::all();

        if ($users->isEmpty() || $stockAmounts->isEmpty() || $warehouses->isEmpty()) {
            $this->command->warn('Required data missing (users, stock_amounts, warehouses).');
            return;
        }

        foreach (range(1, 10) as $i) {
            $stockAmount = $stockAmounts->random();
            $warehouse = $warehouses->random();
            $user = $users->random();

            $isTransfer = rand(0, 1) === 1;
            $toWarehouse = $isTransfer ? $warehouses->where('id', '!=', $warehouse->id)->random() : null;

            StockTransaction::create([
                'stock_amount_id' => $stockAmount->id,
                'warehouse_id' => $warehouse->id,
                'quantity' => rand(1, 100),
                'type' => rand(0, 1) ? 'in' : 'out',
                'is_adjustment' => rand(0, 1),
                'to_warehouse_id' => $toWarehouse?->id,
                'destination' => $isTransfer ? 'transfer' : (rand(0, 1) ? 'lost' : 'add'),
                'note' => 'Contoh transaksi ' . $i,
                'quantity_before' => rand(0, 100),
                'quantity_after' => rand(100, 200),
                'user_id' => $user->id,
            ]);
        }
    }
}
