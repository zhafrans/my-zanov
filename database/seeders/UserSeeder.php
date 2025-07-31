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
        // Helper untuk membuat user dengan nama unik
        $createdUsernames = [];

        // Map role name ke role_id (pastikan nama role sesuai isi tabel)
        $roleMap = DB::table('user_roles')->pluck('id', 'name');

        // Buat user SUPERADMIN
        $superadmins = ['ZHAFRAN', 'SITI', 'IMAM', 'NAUFAL'];
        foreach ($superadmins as $name) {
            $username = strtolower($name);
            if (in_array($username, $createdUsernames)) {   
                continue;
            }
            User::create([
                'name' => $name,
                'code' => fake()->unique()->numerify('###'),
                'username' => $username,
                'email' => "{$username}@example.com",
                'password' => Hash::make('password'),
                'address' => fake()->address(),
                'profile_image' => 'default.png',
                'role_id' => $roleMap['SUPERADMIN'] ?? 1,
                'is_active' => 1,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
            $createdUsernames[] = $username;
        }

        // Buat user ADMIN
        $admins = ['AFIF'];
        foreach ($admins as $name) {
            $username = strtolower($name);
            if (in_array($username, $createdUsernames)) {
                continue;
            }
            User::create([
                'name' => $name,
                'code' => fake()->unique()->numerify('###'),
                'username' => $username,
                'email' => "{$username}@example.com",
                'password' => Hash::make('password'),
                'address' => fake()->address(),
                'profile_image' => 'default.png',
                'role_id' => $roleMap['ADMIN'] ?? 2,
                'is_active' => 1,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
            $createdUsernames[] = $username;
        }

        // Buat user SALES
        $sales = ['UMI', 'ATI', 'INTO', 'NISYA', 'LUKMAN', 'WINDA', 'RESTI', 'ANNA'];
        foreach ($sales as $name) {
            $username = strtolower($name);
            if (in_array($username, $createdUsernames)) {
                continue;
            }
            User::create([
                'name' => $name,
                'code' => fake()->unique()->numerify('###'),
                'username' => $username,
                'email' => "{$username}@example.com",
                'password' => Hash::make('password'),
                'address' => fake()->address(),
                'profile_image' => 'default.png',
                'role_id' => $roleMap['SALES'] ?? 3,
                'is_active' => 1,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);
            $createdUsernames[] = $username;
        }

        // Buat user COLLECTOR
        $collectors = ['UMI', 'LUKMAN', 'IRFAN', 'NISYA', 'INTO', 'IRIS', 'WINDA', 'TONI', 'ANA'];
        foreach ($collectors as $name) {
            $usernameBase = strtolower($name);
            $username = in_array($usernameBase, $createdUsernames) ? $usernameBase . '_coll' : $usernameBase;

            User::create([
                'name' => $name,
                'code' => fake()->unique()->numerify('###'),
                'username' => $username,
                'email' => "{$username}@example.com",
                'password' => Hash::make('password'),
                'address' => fake()->address(),
                'profile_image' => 'default.png',
                'role_id' => $roleMap['COLLECTOR'] ?? 4,
                'is_active' => 1,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]);

            $createdUsernames[] = $username;
        }
    }
}
