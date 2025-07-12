<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Warehouse;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();

        // Ambil warehouse ID yang type-nya 'CAR'
        $carWarehouses = Warehouse::where('type', 'CAR')->pluck('id', 'name')->toArray();

        // Ambil semua user ID
        $users = User::pluck('id')->toArray();

        // Kendaraan utama (ZANOV) → punya warehouse & seller
        $vehiclesWithWarehouse = [
            'ZANOV 01',
            'ZANOV 02',
            'ZANOV 03',
            'ZANOV 04',
        ];

        foreach ($vehiclesWithWarehouse as $name) {
            DB::table('vehicles')->insert([
                'code' => 'VEH-' . $faker->unique()->numerify('###'),
                'name' => $name,
                'licence_plate' => strtoupper($faker->unique()->bothify('B #### ??')),
                'warehouse_id' => $carWarehouses[$name] ?? null,
                'driver_id' => $faker->randomElement($users),
                'seller_id' => $faker->randomElement($users),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Motor (SUPRA & BLADE) → tanpa warehouse & seller
        $motorcycles = ['SUPRA', 'BLADE'];

        foreach ($motorcycles as $name) {
            DB::table('vehicles')->insert([
                'code' => 'VEH-' . $faker->unique()->numerify('###'),
                'name' => $name,
                'licence_plate' => strtoupper($faker->unique()->bothify('R #### ??')),
                'warehouse_id' => null,
                'driver_id' => $faker->randomElement($users),
                'seller_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
