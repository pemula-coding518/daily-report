<?php

namespace Database\Seeders;

use App\Models\AdminHrdUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminHrdUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Default Administrator Account
        AdminHrdUser::firstOrCreate(
            ['email' => 'admin@kantor.com'],
            [
                'name' => 'Administrator Kantor',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // 2. Default HRD Account
        AdminHrdUser::firstOrCreate(
            ['email' => 'hrd@kantor.com'],
            [
                'name' => 'Tim HRD Kantor',
                'password' => Hash::make('password'),
                'role' => 'hrd',
                'is_active' => true,
            ]
        );
    }
}
