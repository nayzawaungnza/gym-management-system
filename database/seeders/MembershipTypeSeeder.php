<?php

namespace Database\Seeders;

use App\Models\MembershipType;
use Illuminate\Database\Seeder;

class MembershipTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $membershipTypes = [
            [
                'type_name' => 'Basic Monthly',
                'duration_months' => 1,
                'price' => 29.99,
                'description' => 'Basic gym access with standard equipment usage',
                'is_active' => true,
            ],
            [
                'type_name' => 'Premium Monthly',
                'duration_months' => 1,
                'price' => 49.99,
                'description' => 'Premium access including classes and personal training sessions',
                'is_active' => true,
            ],
            [
                'type_name' => 'Basic Annual',
                'duration_months' => 12,
                'price' => 299.99,
                'description' => 'Annual basic membership with 2 months free',
                'is_active' => true,
            ],
            [
                'type_name' => 'Premium Annual',
                'duration_months' => 12,
                'price' => 499.99,
                'description' => 'Annual premium membership with all benefits included',
                'is_active' => true,
            ],
            [
                'type_name' => 'Student Monthly',
                'duration_months' => 1,
                'price' => 19.99,
                'description' => 'Discounted membership for students with valid ID',
                'is_active' => true,
            ],
            [
                'type_name' => 'Senior Monthly',
                'duration_months' => 1,
                'price' => 24.99,
                'description' => 'Special pricing for seniors (65+)',
                'is_active' => true,
            ],
        ];

        foreach ($membershipTypes as $type) {
            MembershipType::create($type);
        }
    }
}
