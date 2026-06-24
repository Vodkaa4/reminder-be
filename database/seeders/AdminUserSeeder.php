<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('Admin123'),
                'email_verified_at' => now(),
                'role'     => 'admin',
            ]
        );

        // Create additional test users
        User::updateOrCreate(
            ['email' => 'test@email.com'],
            [
                'name'     => 'Test User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role'     => 'manager',
            ]
        );

        User::updateOrCreate(
            ['email' => 'unverified@email.com'],
            [
                'name'     => 'Unverified User',
                'password' => Hash::make('password'),
                'email_verified_at' => null,
                'role'     => 'manager',
            ]
        );

        User::updateOrCreate(
            ['email' => 'hrd@email.com'],
            [
                'name'     => 'Admin HRD',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role'     => 'hrd',
            ]
        );

        User::updateOrCreate(
            ['email' => 'legal@email.com'],
            [
                'name'     => 'Admin Legal',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role'     => 'legal',
            ]
        );
    }
}
