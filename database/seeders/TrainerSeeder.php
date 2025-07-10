<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class TrainerSeeder extends Seeder
{
    /**
     * Run the seeder.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get user IDs where is_admin = 2 (trainers)
        $trainerUsers = DB::table('users')
            ->where('is_admin', 2)
            ->pluck('id')
            ->toArray();

        // If no trainer users exist, create some
        if (empty($trainerUsers)) {
            for ($i = 0; $i < 5; $i++) {
                $userId = DB::table('users')->insertGetId([
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'password' => bcrypt('password'),
                    'is_admin' => 2,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $trainerUsers[] = $userId;
            }
        }

        $specializations = [
            'Strength Training',
            'Cardio Fitness',
            'Yoga',
            'Pilates',
            'CrossFit',
            'Nutrition Coaching',
            'Rehabilitation'
        ];

        $certifications = [
            ['name' => 'NASM Certified Personal Trainer', 'year' => 2020],
            ['name' => 'ACE Fitness Instructor', 'year' => 2019],
            ['name' => 'CrossFit Level 1', 'year' => 2021],
            ['name' => 'Yoga Alliance RYT-200', 'year' => 2018]
        ];

        foreach ($trainerUsers as $userId) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            
            DB::table('trainers')->insert([
                'id' => Str::uuid(),
                'user_id' => $userId,
                'trainer_id' => 'TR' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'specialization' => $faker->randomElement($specializations),
                'certifications' => json_encode($faker->randomElements($certifications, rand(1, 3))),
                'hire_date' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
                'hourly_rate' => $faker->randomFloat(2, 30, 100),
                'bio' => $faker->paragraph(3),
                'profile_photo' => $faker->imageUrl(200, 200, 'people'),
                'is_active' => $faker->boolean(90), // 90% chance of being active
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}