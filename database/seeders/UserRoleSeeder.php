<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_roles')->insert([
            [
                'code' => 'SUPERADMIN',
                'name' => 'Super Administrator',
                'scope' => 'GLOBAL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ADMIN',
                'name' => 'Administrator',
                'scope' => 'GLOBAL',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SALES',
                'name' => 'Sales',
                'scope' => 'SALES',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'DRIVER',
                'name' => 'Driver',
                'scope' => 'SALES',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'COLLECTOR',
                'name' => 'Collector',
                'scope' => 'SALES',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
