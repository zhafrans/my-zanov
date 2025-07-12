<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserRoleSeeder::class,
            UserSeeder::class,
            WarehouseSeeder::class,
            VehicleSeeder::class,
            ColorSeeder::class,
            SizeSeeder::class
        ]);
    }
}
