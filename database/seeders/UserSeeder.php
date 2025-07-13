<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            // Admin (local login)
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+1234567890',
                'is_email_verified' => true,
                'login_provider' => 'local',
                'last_login_at' => now()->subDays(2),
            ],

            // Vendor (Google login)
            [
                'first_name' => 'Sarah',
                'last_name' => 'Vendor',
                'email' => 'sarah.vendor@example.com',
                'password' => null,
                'role' => 'vendor',
                'phone' => '+1987654321',
                'is_email_verified' => true,
                'login_provider' => 'google',
                'provider_id' => 'google_'.Str::random(21),
                'last_login_at' => now()->subHours(3),
                'facebook_url' => 'https://facebook.com/sarahvendor',
            ],

            // Client (Facebook login)
            [
                'first_name' => 'John',
                'last_name' => 'Client',
                'email' => 'john.client@example.com',
                'password' => null,
                'role' => 'client',
                'phone' => '+1555123456',
                'is_email_verified' => true,
                'login_provider' => 'facebook',
                'provider_id' => 'facebook_'.Str::random(17),
                'last_login_at' => now()->subMinutes(30),
                'instagram_url' => 'https://instagram.com/johnclient',
            ],

            // Client (local login - unverified)
            [
                'first_name' => 'Emily',
                'last_name' => 'Smith',
                'email' => 'emily@example.com',
                'password' => Hash::make('password123'),
                'role' => 'client',
                'phone' => '+44123456789',
                'is_email_verified' => false,
                'login_provider' => 'local',
                'email_verification_token' => Str::random(60),
                'email_verification_token_expires_at' => now()->addHours(24),
                'last_login_at' => now()->subDays(5),
                'address' => '123 Main St',
                'city' => 'London',
                'country' => 'UK',
            ],

            // Vendor (local login - deactivated)
            [
                'first_name' => 'Robert',
                'last_name' => 'Vendor',
                'email' => 'robert@vendor.com',
                'password' => Hash::make('securepassword'),
                'role' => 'vendor',
                'phone' => '+44987654321',
                'is_email_verified' => true,
                'login_provider' => 'local',
                'last_login_at' => now()->subWeeks(2),
                'is_active' => false,
                'tiktok_url' => 'https://tiktok.com/@robertvendor',
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}