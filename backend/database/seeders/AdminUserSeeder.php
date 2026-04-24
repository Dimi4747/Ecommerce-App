<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ecommerce.com'],
            [
                'name' => 'Admin User',
                'email' => 'admin@ecommerce.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567890',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@ecommerce.com'],
            [
                'name' => 'Staff User',
                'email' => 'staff@ecommerce.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567891',
                'role' => 'staff',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'customer@ecommerce.com'],
            [
                'name' => 'Customer User',
                'email' => 'customer@ecommerce.com',
                'password' => Hash::make('password'),
                'phone' => '+1234567892',
                'role' => 'customer',
                'email_verified_at' => now(),
            ]
        );
    }
}
