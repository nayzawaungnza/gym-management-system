<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            // Core system setup first
            RolePermissionSeeder::class,
            MembershipTypeSeeder::class,
            PaymentMethodTableSeeder::class,
            
            // Then create users
            UserSeeder::class,
            
            // Then create dependent records
            TrainerSeeder::class,
            MemberSeeder::class,
            GymClassSeeder::class,
        ]);
    }
}