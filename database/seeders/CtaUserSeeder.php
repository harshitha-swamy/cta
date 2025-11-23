<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CtaUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cta_users')->insert([
            [
                'name' => 'Approver',
                'email' => 'approver@test.com',
                'password' => Hash::make('password'),
                'role' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Developer',
                'email' => 'developer@test.com',
                'password' => Hash::make('password'),
                'role' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
