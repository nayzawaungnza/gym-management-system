<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\User;
use App\Models\MembershipType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $membershipTypes = MembershipType::all();

        if ($membershipTypes->isEmpty()) {
            $this->command->warn('No membership types found. Please run MembershipTypeSeeder first.');
            return;
        }

        $members = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'phone' => '+1-555-0101',
                'date_of_birth' => '1990-05-15',
                'gender' => 'male',
                'address' => '123 Main St, Anytown, ST 12345',
                'emergency_contact_name' => 'Jane Smith',
                'emergency_contact_phone' => '+1-555-0102',
                'medical_conditions' => ['None'], // Changed to array
                'fitness_goals' => ['Weight loss and muscle building'], // Changed to array
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@example.com',
                'phone' => '+1-555-0201',
                'date_of_birth' => '1985-08-22',
                'gender' => 'female',
                'address' => '456 Oak Ave, Somewhere, ST 67890',
                'emergency_contact_name' => 'Mike Johnson',
                'emergency_contact_phone' => '+1-555-0202',
                'medical_conditions' => ['Mild asthma'], // Changed to array
                'fitness_goals' => ['Improve cardiovascular health'], // Changed to array
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@example.com',
                'phone' => '+1-555-0301',
                'date_of_birth' => '1988-11-10',
                'gender' => 'male',
                'address' => '789 Pine Rd, Nowhere, ST 54321',
                'emergency_contact_name' => 'Lisa Brown',
                'emergency_contact_phone' => '+1-555-0302',
                'medical_conditions' => ['High blood pressure'], // Changed to array
                'fitness_goals' => ['General fitness'], // Changed to array
            ]
        ];

        $createdCount = 0;
        $updatedCount = 0;

        foreach ($members as $memberData) {
            // Create or update user
            $user = User::updateOrCreate(
                ['email' => $memberData['email']],
                [
                    'name' => $memberData['name'],
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'created_at' => now()->subDays(rand(1, 60)),
                    'updated_at' => now()->subDays(rand(0, 10)),
                ]
            );
            
            // Assign member role if not already assigned
            if (!$user->hasRole('member')) {
                $user->assignRole('member');
            }

            // Prepare member data
            $joinDate = now()->subDays(rand(30, 365));
            $nameParts = explode(' ', $memberData['name'], 2);
            $membershipType = $membershipTypes->random();

            // Create or update member profile
            $member = Member::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'membership_type_id' => $membershipType->id,
                    'member_id' => 'GYM' . str_pad(Member::count() + 1, 6, '0', STR_PAD_LEFT),
                    'first_name' => $nameParts[0],
                    'last_name' => $nameParts[1] ?? '',
                    'email' => $memberData['email'],
                    'phone' => $memberData['phone'],
                    'date_of_birth' => Carbon::parse($memberData['date_of_birth']),
                    'gender' => $memberData['gender'],
                    'address' => $memberData['address'],
                    'emergency_contact_name' => $memberData['emergency_contact_name'],
                    'emergency_contact_phone' => $memberData['emergency_contact_phone'],
                    'join_date' => $joinDate,
                    'membership_start_date' => $joinDate,
                    'membership_end_date' => $joinDate->copy()->addMonths($membershipType->duration_months),
                    'status' => 'active',
                    'medical_conditions' => $memberData['medical_conditions'], // Directly using array
                    'fitness_goals' => $memberData['fitness_goals'], // Directly using array
                    'created_at' => $joinDate,
                    'updated_at' => now()->subDays(rand(0, 5)),
                ]
            );

            if ($member->wasRecentlyCreated) {
                $createdCount++;
                $this->command->info("Created member: {$user->name}");
            } else {
                $updatedCount++;
                $this->command->line("Updated member: {$user->name}");
            }
        }

        $this->command->info("\nMembers seeding completed!");
        $this->command->info("Total members processed: " . count($members));
        $this->command->info("New members created: {$createdCount}");
        $this->command->info("Existing members updated: {$updatedCount}");

        // Summary by membership type
        $membersByType = Member::with('membershipType')->get()->groupBy('membershipType.type_name');
        $this->command->info("\nMembers by membership type:");
        foreach ($membersByType as $type => $typeMembers) {
            $this->command->info("- {$type}: " . count($typeMembers) . " members");
        }
    }
}