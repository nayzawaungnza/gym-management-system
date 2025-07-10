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
                'medical_conditions' => 'None',
                'fitness_goals' => 'Weight loss and muscle building',
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
                'medical_conditions' => 'Mild asthma',
                'fitness_goals' => 'Improve cardiovascular health',
            ],
            // ... add other members as needed ...
        ];

        foreach ($members as $memberData) {
            // Create user account
            $user = User::create([
                'name' => $memberData['name'],
                'email' => $memberData['email'],
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'is_active' => true, // Assuming the users table has this column
                'created_at' => now()->subDays(rand(1, 60)),
                'updated_at' => now()->subDays(rand(0, 10)),
            ]);

            // Assign role
            $user->assignRole('member');

            // Create member profile
            $membershipType = $membershipTypes->random();
            $joinDate = now()->subDays(rand(30, 365));
            $nameParts = explode(' ', $memberData['name'], 2);

            Member::create([
                'user_id' => $user->id,
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
                'medical_conditions' => json_encode([$memberData['medical_conditions']]),
                'fitness_goals' => json_encode([$memberData['fitness_goals']]),
                'created_at' => $joinDate,
                'updated_at' => now()->subDays(rand(0, 5)),
            ]);

            $this->command->info("Created member: {$user->name}");
        }

        $this->command->info('Members seeded successfully!');
        $this->command->info('Total members created: ' . count($members));

        // Summary
        $membersByType = Member::with('membershipType')->get()->groupBy('membershipType.name');
        $this->command->info("\nMembers by membership type:");
        foreach ($membersByType as $type => $typeMembers) {
            $this->command->info("- {$type}: " . count($typeMembers) . " members");
        }
    }
}