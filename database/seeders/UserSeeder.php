<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@gym.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin' => 1,
                'is_active' => true,
            ]
        );
        $admin->assignRole('admin');

        // Create Manager User
        $manager = User::updateOrCreate(
            ['email' => 'manager@gym.com'],
            [
                'name' => 'Gym Manager',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin' => 1,
                'is_active' => true,
            ]
        );
        $manager->assignRole('manager');

        // Create Trainer User
        $trainer = User::updateOrCreate(
            ['email' => 'trainer@gym.com'],
            [
                'name' => 'John Trainer',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin' => 2,
                'is_active' => true,
            ]
        );
        $trainer->assignRole('trainer');

        // Create Member User
        $member = User::updateOrCreate(
            ['email' => 'member@gym.com'],
            [
                'name' => 'Jane Member',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_admin' => 0,
                'is_active' => true,
            ]
        );
        $member->assignRole('member');

        // Additional sample users
        $users = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'role' => 'member'
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike@example.com',
                'role' => 'trainer'
            ],
            [
                'name' => 'Lisa Brown',
                'email' => 'lisa@example.com',
                'role' => 'member'
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'is_admin' => $userData['role'] === 'trainer' ? 2 : 0,
                    'is_active' => true,
                ]
            );
            $user->assignRole($userData['role']);
        }
    }
}