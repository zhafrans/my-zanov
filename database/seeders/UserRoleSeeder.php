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
        ]);
    }
}
