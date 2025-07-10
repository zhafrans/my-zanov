<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat satu user admin terlebih dahulu
        User::create([
            'name' => 'Zhafran',
            'code' => fake()->numerify('###'),
            'username' => 'admin',
            'email' => 'adm@example.com',
            'password' => Hash::make('password'),
            'address' => 'Jl. Contoh No.123',
            'profile_image' => 'default.png',
            'role_id' => 1, // pastikan role_id 1 ada di tabel user_roles
            'is_active' => 1,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        // Buat 50 user tambahan
        for ($i = 1; $i <= 50; $i++) {
            User::create([
                'name' => fake()->name(),
                'code' => fake()->numerify('###'),
                'username' => 'user' . $i,
                'email' => "user{$i}@example.com",
                'password' => Hash::make('password'),
                'address' => fake()->address(),
                'profile_image' => 'default.png',
                'role_id' => rand(1, 2), // ganti sesuai role yang tersedia
                'is_active' => 1,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
        }
    }
}
