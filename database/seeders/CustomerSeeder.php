<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Village;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua desa lengkap dengan relasi sampai provinsi
        $villages = Village::with(['subdistrict.city.province'])->get();

        if ($villages->isEmpty()) {
            $this->command->warn('Data desa belum tersedia.');
            return;
        }

        for ($i = 1; $i <= 30; $i++) {
            $village = $villages->random();
            $subdistrict = $village->subdistrict;
            $city = $subdistrict->city;
            $province = $city->province;

            Customer::create([
                'code'           => 'CUST-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'name'           => fake()->name(),
                'address'        => fake()->address(),
                'phone'          => fake()->phoneNumber(),
                'village_id'     => $village->id,
                'subdistrict_id' => $subdistrict->id,
                'city_id'        => $city->id,
                'province_id'    => $province->id,
            ]);
        }
    }
}
