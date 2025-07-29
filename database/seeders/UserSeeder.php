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
        // System Users
        $systemUsers = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@gym.com',
                'password' => 'password',
                'role' => 'Admin',
                'is_admin' => 1
            ],
            [
                'name' => 'Gym Manager',
                'email' => 'manager@gym.com',
                'password' => 'password',
                'role' => 'Admin',
                'is_admin' => 1
            ],
            [
                'name' => 'Head Trainer',
                'email' => 'trainer@gym.com',
                'password' => 'password',
                'role' => 'Trainer',
                'is_admin' => 2
            ],
            [
                'name' => 'Sample Member',
                'email' => 'member@gym.com',
                'password' => 'password',
                'role' => 'Member',
                'is_admin' => 0
            ],
        ];

        foreach ($systemUsers as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => now(),
                    'is_admin' => $userData['is_admin'],
                    'is_active' => true,
                ]
            );
            $user->assignRole($userData['role']);
        }

        // Additional sample users
        $sampleUsers = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah@example.com',
                'password' => 'password',
                'role' => 'member'
            ],
            [
                'name' => 'Mike Wilson',
                'email' => 'mike@example.com',
                'password' => 'password',
                'role' => 'trainer'
            ],
            [
                'name' => 'Lisa Brown',
                'email' => 'lisa@example.com',
                'password' => 'password',
                'role' => 'member'
            ],
        ];

        foreach ($sampleUsers as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => now(),
                    'is_admin' => $userData['role'] === 'trainer' ? 2 : 0,
                    'is_active' => true,
                ]
            );
            $user->assignRole($userData['role']);
        }
    }
}