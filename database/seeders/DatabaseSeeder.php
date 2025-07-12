<?php

namespace Database\Seeders;

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
            SizeSeeder::class,
            ProductSeeder::class,
            HeelSeeder::class,
            ProductVariantSeeder::class,
            ProvinceSeeder::class,
            CitySeeder::class,
            SubdistrictSeeder::class,
            VillageSeeder::class,
        ]);
    }
}
