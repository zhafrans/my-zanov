<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Heel;

class HeelSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'code' => 'F',
                'name' => 'Flat',
                'description' => '0 cm / flat heel',
            ],
            [
                'code' => '3',
                'name' => '3 cm',
                'description' => 'Heel height 3 centimeters',
            ],
            [
                'code' => '5',
                'name' => '5 cm',
                'description' => 'Heel height 5 centimeters',
            ],
            [
                'code' => '7',
                'name' => '7 cm',
                'description' => 'Heel height 7 centimeters',
            ],
        ];

        foreach ($data as $item) {
            Heel::create($item);
        }
    }
}
