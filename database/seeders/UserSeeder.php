<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@retcrm.com',
            'role' => 'admin',
            'password' => Hash::make('123@abc'),
            'email_verified_at' => now(),
        ]);

        // Shop Manager User
        User::create([
            'name' => 'Shop Manager',
            'email' => 'shop@retcrm.com',
            'role' => 'shop_manager',
            'password' => Hash::make('123@abc'),
            'email_verified_at' => now(),
        ]);

        // Regional Manager User
        User::create([
            'name' => 'Regional Manager',
            'email' => 'regional@retcrm.com',
            'role' => 'regional_manager',
            'password' => Hash::make('123@abc'),
            'email_verified_at' => now(),
        ]);

        // Retail Manager User
        User::create([
            'name' => 'Retail Manager',
            'email' => 'retail@retcrm.com',
            'role' => 'retail_manager',
            'password' => Hash::make('123@abc'),
            'email_verified_at' => now(),
        ]);

        // Nguyen Dong User
        User::create([
            'name' => 'Nguyen Dong',
            'email' => 'nguyendong@dalathasfarm.com',
            'role' => 'admin',
            'password' => Hash::make('123456@abc'),
            'email_verified_at' => now(),
        ]);
    }
}
