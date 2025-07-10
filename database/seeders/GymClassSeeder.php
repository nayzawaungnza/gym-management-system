<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class GymClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active trainers with their specializations
        $trainers = DB::table('trainers')
            ->where('is_active', true)
            ->select('id', 'specialization')
            ->get();

        if ($trainers->isEmpty()) {
            $this->command->warn('No active trainers found. Please run TrainerSeeder first.');
            return;
        }

        $classes = [
            // Cardio Classes
            [
                'class_name' => 'High-Intensity Interval Training (HIIT)',
                'description' => 'A high-intensity workout alternating intense bursts of activity with rest periods.',
                'class_type' => 'Cardio',
                'schedule_day' => 'monday',
                'start_time' => '08:00:00',
                'end_time' => '08:45:00',
                'duration_minutes' => 45,
                'max_capacity' => 20,
                'price' => 25.00,
                'room' => 'Studio A',
                'equipment_needed' => 'Dumbbells, Exercise Mat, Timer',
                'difficulty_level' => 'intermediate',
                'specialization_match' => ['Cardio Fitness', 'CrossFit'],
            ],
            [
                'class_name' => 'Zumba Fitness',
                'description' => 'Energetic dance fitness combining Latin and international music.',
                'class_type' => 'Dance Fitness',
                'schedule_day' => 'tuesday',
                'start_time' => '18:00:00',
                'end_time' => '19:00:00',
                'duration_minutes' => 60,
                'max_capacity' => 25,
                'price' => 20.00,
                'room' => 'Studio B',
                'equipment_needed' => 'Comfortable shoes, Water bottle',
                'difficulty_level' => 'beginner',
                'specialization_match' => ['Cardio Fitness'],
            ],
            [
                'class_name' => 'Spin Class',
                'description' => 'Indoor cycling workout with energizing music.',
                'class_type' => 'Cardio',
                'schedule_day' => 'wednesday',
                'start_time' => '07:00:00',
                'end_time' => '07:45:00',
                'duration_minutes' => 45,
                'max_capacity' => 15,
                'price' => 22.00,
                'room' => 'Cycling Studio',
                'equipment_needed' => 'Stationary bike, Water bottle, Towel',
                'difficulty_level' => 'intermediate',
                'specialization_match' => ['Cardio Fitness'],
            ],

            // Strength Training Classes
            [
                'class_name' => 'CrossFit Fundamentals',
                'description' => 'Introduction to CrossFit focusing on functional movements.',
                'class_type' => 'Strength Training',
                'schedule_day' => 'thursday',
                'start_time' => '17:30:00',
                'end_time' => '18:30:00',
                'duration_minutes' => 60,
                'max_capacity' => 12,
                'price' => 35.00,
                'room' => 'Functional Zone',
                'equipment_needed' => 'Barbells, Dumbbells, Kettlebells, Pull-up bar',
                'difficulty_level' => 'beginner',
                'specialization_match' => ['CrossFit', 'Strength Training'],
            ],
            [
                'class_name' => 'Powerlifting Basics',
                'description' => 'Learn squat, bench press, and deadlift techniques.',
                'class_type' => 'Strength Training',
                'schedule_day' => 'friday',
                'start_time' => '19:00:00',
                'end_time' => '20:15:00',
                'duration_minutes' => 75,
                'max_capacity' => 8,
                'price' => 40.00,
                'room' => 'Weight Room',
                'equipment_needed' => 'Olympic barbell, Weight plates, Power rack',
                'difficulty_level' => 'intermediate',
                'specialization_match' => ['Strength Training'],
            ],
            [
                'class_name' => 'Functional Strength Training',
                'description' => 'Build strength through everyday movement patterns.',
                'class_type' => 'Strength Training',
                'schedule_day' => 'saturday',
                'start_time' => '09:00:00',
                'end_time' => '09:50:00',
                'duration_minutes' => 50,
                'max_capacity' => 15,
                'price' => 28.00,
                'room' => 'Functional Zone',
                'equipment_needed' => 'Resistance bands, Medicine balls, TRX straps',
                'difficulty_level' => 'beginner',
                'specialization_match' => ['Strength Training'],
            ],

            // Yoga & Flexibility Classes
            [
                'class_name' => 'Hatha Yoga',
                'description' => 'Gentle yoga focusing on postures and breathing.',
                'class_type' => 'Yoga',
                'schedule_day' => 'sunday',
                'start_time' => '10:00:00',
                'end_time' => '11:00:00',
                'duration_minutes' => 60,
                'max_capacity' => 20,
                'price' => 18.00,
                'room' => 'Yoga Studio',
                'equipment_needed' => 'Yoga mat, Yoga blocks, Yoga strap',
                'difficulty_level' => 'beginner',
                'specialization_match' => ['Yoga'],
            ],
            [
                'class_name' => 'Vinyasa Flow Yoga',
                'description' => 'Dynamic yoga linking breath with movement.',
                'class_type' => 'Yoga',
                'schedule_day' => 'monday',
                'start_time' => '18:30:00',
                'end_time' => '19:45:00',
                'duration_minutes' => 75,
                'max_capacity' => 18,
                'price' => 22.00,
                'room' => 'Yoga Studio',
                'equipment_needed' => 'Yoga mat, Water bottle',
                'difficulty_level' => 'intermediate',
                'specialization_match' => ['Yoga'],
            ],
            [
                'class_name' => 'Pilates Mat Class',
                'description' => 'Core-focused workout emphasizing alignment.',
                'class_type' => 'Pilates',
                'schedule_day' => 'wednesday',
                'start_time' => '17:00:00',
                'end_time' => '17:55:00',
                'duration_minutes' => 55,
                'max_capacity' => 16,
                'price' => 24.00,
                'room' => 'Studio A',
                'equipment_needed' => 'Exercise mat, Pilates ring, Small weights',
                'difficulty_level' => 'beginner',
                'specialization_match' => ['Pilates'],
            ],

            // Specialized Classes
            [
                'class_name' => 'Aqua Aerobics',
                'description' => 'Low-impact water-based exercise.',
                'class_type' => 'Aqua Fitness',
                'schedule_day' => 'tuesday',
                'start_time' => '09:00:00',
                'end_time' => '09:45:00',
                'duration_minutes' => 45,
                'max_capacity' => 12,
                'price' => 20.00,
                'room' => 'Pool Area',
                'equipment_needed' => 'Swimwear, Water weights, Pool noodles',
                'difficulty_level' => 'beginner',
                'specialization_match' => ['Cardio Fitness'],
            ],
            [
                'class_name' => 'Senior Fitness',
                'description' => 'Gentle exercise for older adults.',
                'class_type' => 'Senior Fitness',
                'schedule_day' => 'thursday',
                'start_time' => '10:00:00',
                'end_time' => '10:45:00',
                'duration_minutes' => 45,
                'max_capacity' => 15,
                'price' => 15.00,
                'room' => 'Studio B',
                'equipment_needed' => 'Chair, Light weights, Resistance bands',
                'difficulty_level' => 'beginner',
                'specialization_match' => ['Rehabilitation'],
            ],
            [
                'class_name' => 'Boxing Fundamentals',
                'description' => 'Learn basic boxing techniques for fitness.',
                'class_type' => 'Martial Arts',
                'schedule_day' => 'friday',
                'start_time' => '17:00:00',
                'end_time' => '18:00:00',
                'duration_minutes' => 60,
                'max_capacity' => 14,
                'price' => 30.00,
                'room' => 'Boxing Ring',
                'equipment_needed' => 'Boxing gloves, Hand wraps, Heavy bag',
                'difficulty_level' => 'beginner',
                'specialization_match' => ['Cardio Fitness'],
            ],
            [
                'class_name' => 'Bootcamp Challenge',
                'description' => 'Military-inspired strength and cardio workout.',
                'class_type' => 'Group Fitness',
                'schedule_day' => 'saturday',
                'start_time' => '08:00:00',
                'end_time' => '08:50:00',
                'duration_minutes' => 50,
                'max_capacity' => 20,
                'price' => 26.00,
                'room' => 'Outdoor Area',
                'equipment_needed' => 'Exercise mat, Water bottle',
                'difficulty_level' => 'advanced',
                'specialization_match' => ['CrossFit', 'Strength Training'],
            ],
            [
                'class_name' => 'Stretching & Mobility',
                'description' => 'Improve flexibility and joint mobility.',
                'class_type' => 'Recovery',
                'schedule_day' => 'sunday',
                'start_time' => '17:00:00',
                'end_time' => '17:30:00',
                'duration_minutes' => 30,
                'max_capacity' => 25,
                'price' => 12.00,
                'room' => 'Studio A',
                'equipment_needed' => 'Exercise mat, Foam roller, Stretching strap',
                'difficulty_level' => 'beginner',
                'specialization_match' => ['Rehabilitation', 'Yoga'],
            ],
            [
                'class_name' => 'Barre Fitness',
                'description' => 'Ballet-inspired controlled movement workout.',
                'class_type' => 'Barre',
                'schedule_day' => 'wednesday',
                'start_time' => '18:30:00',
                'end_time' => '19:25:00',
                'duration_minutes' => 55,
                'max_capacity' => 18,
                'price' => 25.00,
                'room' => 'Studio B',
                'equipment_needed' => 'Ballet barre, Light weights, Exercise mat',
                'difficulty_level' => 'intermediate',
                'specialization_match' => ['Pilates'],
            ],
        ];

        $successfulInserts = 0;
        $failedInserts = 0;

        foreach ($classes as $classData) {
            try {
                // Find suitable trainer based on specialization
                $suitableTrainers = $trainers->filter(function ($trainer) use ($classData) {
                    return in_array($trainer->specialization, $classData['specialization_match']);
                });

                if ($suitableTrainers->isEmpty()) {
                    $trainer = $trainers->random();
                    $this->command->warn("No specialization match for {$classData['class_name']}. Using random trainer.");
                } else {
                    $trainer = $suitableTrainers->random();
                }

                // Calculate current capacity (50-80% of max capacity)
                $currentCapacity = max(0, min($classData['max_capacity'], 
                    rand($classData['max_capacity'] * 0.5, $classData['max_capacity'] * 0.8)));

                // Generate realistic timestamps
                $createdAt = Carbon::now()->subDays(rand(1, 30));
                $updatedAt = Carbon::now()->subDays(rand(0, 5));

                DB::table('gym_classes')->insert([
                    'id' => Str::uuid(),
                    'trainer_id' => $trainer->id,
                    'class_name' => $classData['class_name'],
                    'description' => $classData['description'],
                    'class_type' => $classData['class_type'],
                    'schedule_day' => $classData['schedule_day'],
                    'start_time' => $classData['start_time'],
                    'end_time' => $classData['end_time'],
                    'duration_minutes' => $classData['duration_minutes'],
                    'max_capacity' => $classData['max_capacity'],
                    'current_capacity' => $currentCapacity,
                    'price' => $classData['price'],
                    'room' => $classData['room'],
                    'equipment_needed' => $classData['equipment_needed'],
                    'difficulty_level' => $classData['difficulty_level'],
                    'is_active' => true,
                    'created_at' => $createdAt,
                    'updated_at' => $updatedAt,
                ]);

                $successfulInserts++;
                $this->command->info("Created: {$classData['class_name']} (Trainer ID: {$trainer->id})");
            } catch (\Exception $e) {
                $failedInserts++;
                $this->command->error("Failed to create {$classData['class_name']}: {$e->getMessage()}");
            }
        }

        // Display summary
        $this->command->info("\nSeeding Summary:");
        $this->command->info("Total classes attempted: " . count($classes));
        $this->command->info("Successful inserts: {$successfulInserts}");
        $this->command->info("Failed inserts: {$failedInserts}");

        // Group by class type for summary
        $types = collect($classes)->groupBy('class_type');
        $this->command->info("\nClasses by Type:");
        foreach ($types as $type => $typeClasses) {
            $this->command->info("- {$type}: " . count($typeClasses));
        }
    }
}