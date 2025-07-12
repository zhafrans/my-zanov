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

        // Buat 500 user tambahan menggunakan chunk untuk optimasi
        $totalUsers = 500;
        $chunkSize = 50; // Jumlah user per batch
        
        $userChunks = ceil($totalUsers / $chunkSize);
        
        for ($i = 0; $i < $userChunks; $i++) {
            $users = [];
            $currentChunkSize = ($i == $userChunks - 1) ? ($totalUsers % $chunkSize ?: $chunkSize) : $chunkSize;
            
            for ($j = 1; $j <= $currentChunkSize; $j++) {
                $userNumber = ($i * $chunkSize) + $j;
                $users[] = [
                    'name' => fake()->name(),
                    'code' => fake()->unique()->numerify('#####'),
                    'username' => 'user' . $userNumber,
                    'email' => "user{$userNumber}@example.com",
                    'password' => Hash::make('password'),
                    'address' => fake()->address(),
                    'profile_image' => 'default.png',
                    'role_id' => rand(1, 2), // ganti sesuai role yang tersedia
                    'is_active' => 1,
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            User::insert($users);
        }
    }
}