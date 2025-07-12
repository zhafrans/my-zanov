<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat user admin terlebih dahulu
        User::create([
            'name' => 'Zhafran',
            'code' => fake()->numerify('###'),
            'username' => 'admin',
            'email' => 'adm@example.com',
            'password' => Hash::make('password'),
            'address' => 'Jl. Contoh No.123',
            'profile_image' => 'default.png',
            'role_id' => 1, // pastikan role_id 1 ada
            'is_active' => 1,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        // Ambil semua role dari tabel user_roles
        $roles = DB::table('user_roles')->get(); // pastikan nama tabelnya benar

        $counter = 1;

        // Buat satu user untuk setiap role
        foreach ($roles as $role) {
            User::create([
                'name' => fake()->name(),
                'code' => fake()->unique()->numerify('#####'),
                'username' => 'roleuser' . $counter,
                'email' => "roleuser{$counter}@example.com",
                'password' => Hash::make('password'),
                'address' => fake()->address(),
                'profile_image' => 'default.png',
                'role_id' => $role->id,
                'is_active' => 1,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
            $counter++;
        }

        // Buat user tambahan sebanyak 20 - jumlah role
        $totalToGenerate = 20;
        $remainingUsers = $totalToGenerate - $roles->count();

        for ($i = 1; $i <= $remainingUsers; $i++) {
            User::create([
                'name' => fake()->name(),
                'code' => fake()->unique()->numerify('#####'),
                'username' => 'user' . $i,
                'email' => "user{$i}@example.com",
                'password' => Hash::make('password'),
                'address' => fake()->address(),
                'profile_image' => 'default.png',
                'role_id' => $roles->random()->id,
                'is_active' => 1,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
        }
    }
}
