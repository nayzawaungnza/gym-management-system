<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use App\Models\User;

class TrainerSeeder extends Seeder
{
    /**
     * Run the seeder.
     */
    public function run(): void
    {
        $faker = Faker::create();
        
        // Get existing trainer users
        $trainerUsers = User::where('is_admin', 2)
            ->pluck('id')
            ->toArray();

        // Ensure we have at least 5 trainers
        if (count($trainerUsers) < 5) {
            $needed = 5 - count($trainerUsers);
            for ($i = 0; $i < $needed; $i++) {
                $user = User::create([
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'password' => bcrypt('password'),
                    'is_admin' => 2,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]);
                
                $trainerUsers[] = $user->id;
                
                // Assign trainer role if not already assigned
                if (!$user->hasRole('Trainer')) {
                    $user->assignRole('Trainer');
                }
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
            ['name' => 'Yoga Alliance RYT-200', 'year' => 2018],
            ['name' => 'Precision Nutrition Level 1', 'year' => 2022]
        ];

        foreach ($trainerUsers as $userId) {
            $user = User::find($userId);
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;
            
            // Check if trainer profile already exists
            if (!$user->trainer) {
                DB::table('trainers')->insert([
                    'id' => Str::uuid(),
                    'user_id' => $userId,
                    'trainer_id' => 'TR' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $user->email,
                    'phone' => null,
                    'specialization' => $faker->randomElement($specializations),
                    'certifications' => json_encode($faker->randomElements($certifications, rand(1, 3))),
                    'hire_date' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
                    'hourly_rate' => $faker->randomFloat(2, 30, 100),
                    'bio' => $faker->paragraph(3),
                    'profile_photo' => $faker->imageUrl(200, 200, 'people'),
                    'is_active' => $faker->boolean(90),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Trainers seeded successfully!');
        $this->command->info('Total trainers: ' . count($trainerUsers));
        
        // Summary by specialization
        $specializationCounts = DB::table('trainers')
            ->select('specialization', DB::raw('count(*) as total'))
            ->groupBy('specialization')
            ->get();
            
        $this->command->info("\nTrainers by specialization:");
        foreach ($specializationCounts as $spec) {
            $this->command->info("- {$spec->specialization}: {$spec->total}");
        }
    }
}