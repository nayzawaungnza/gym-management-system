<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipment;
use Carbon\Carbon;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipment = [
            // Cardio Equipment
            [
                'equipment_name' => 'Treadmill Pro X1',
                'equipment_type' => 'Cardio',
                'brand' => 'FitnessTech',
                'model' => 'PTX-2024',
                'serial_number' => 'FT-TM-001',
                'purchase_date' => '2023-01-15',
                'purchase_price' => 3500.00,
                'warranty_expiry' => '2025-01-15',
                'last_maintenance_date' => '2024-06-01',
                'next_maintenance_date' => '2024-09-01',
                'maintenance_interval_days' => 90,
                'location' => 'Cardio Zone A',
                'status' => 'operational',
                'notes' => 'High-performance treadmill with advanced cushioning system',
            ],
            [
                'equipment_name' => 'Elliptical Trainer Elite',
                'equipment_type' => 'Cardio',
                'brand' => 'CardioMax',
                'model' => 'ETE-500',
                'serial_number' => 'CM-EL-002',
                'purchase_date' => '2023-02-20',
                'purchase_price' => 2800.00,
                'warranty_expiry' => '2024-08-20',
                'last_maintenance_date' => '2024-05-15',
                'next_maintenance_date' => '2024-08-15',
                'maintenance_interval_days' => 90,
                'location' => 'Cardio Zone A',
                'status' => 'operational',
                'notes' => 'Low-impact full-body workout machine',
            ],
            [
                'equipment_name' => 'Stationary Bike Pro',
                'equipment_type' => 'Cardio',
                'brand' => 'CycleFit',
                'model' => 'SBP-300',
                'serial_number' => 'CF-SB-003',
                'purchase_date' => '2023-03-10',
                'purchase_price' => 1200.00,
                'warranty_expiry' => '2024-03-10',
                'last_maintenance_date' => '2024-06-10',
                'next_maintenance_date' => '2024-09-10',
                'maintenance_interval_days' => 90,
                'location' => 'Cardio Zone B',
                'status' => 'operational',
                'notes' => 'Quiet magnetic resistance bike',
            ],

            // Strength Training Equipment
            [
                'equipment_name' => 'Olympic Barbell Set',
                'equipment_type' => 'Strength Training',
                'brand' => 'IronCore',
                'model' => 'OBS-45',
                'serial_number' => 'IC-BB-004',
                'purchase_date' => '2023-01-25',
                'purchase_price' => 800.00,
                'warranty_expiry' => '2028-01-25',
                'last_maintenance_date' => '2024-04-01',
                'next_maintenance_date' => '2024-10-01',
                'maintenance_interval_days' => 180,
                'location' => 'Free Weight Area',
                'status' => 'operational',
                'notes' => 'Professional grade Olympic barbell',
            ],
            [
                'equipment_name' => 'Power Rack Station',
                'equipment_type' => 'Strength Training',
                'brand' => 'StrengthMax',
                'model' => 'PRS-2000',
                'serial_number' => 'SM-PR-005',
                'purchase_date' => '2023-02-05',
                'purchase_price' => 2200.00,
                'warranty_expiry' => '2026-02-05',
                'last_maintenance_date' => '2024-05-01',
                'next_maintenance_date' => '2024-11-01',
                'maintenance_interval_days' => 180,
                'location' => 'Free Weight Area',
                'status' => 'operational',
                'notes' => 'Multi-station power rack with pull-up bar',
            ],
            [
                'equipment_name' => 'Adjustable Dumbbell Set',
                'equipment_type' => 'Strength Training',
                'brand' => 'FlexWeight',
                'model' => 'ADS-100',
                'serial_number' => 'FW-DB-006',
                'purchase_date' => '2023-03-15',
                'purchase_price' => 1500.00,
                'warranty_expiry' => '2025-03-15',
                'last_maintenance_date' => '2024-06-15',
                'next_maintenance_date' => '2024-12-15',
                'maintenance_interval_days' => 180,
                'location' => 'Free Weight Area',
                'status' => 'operational',
                'notes' => 'Space-efficient adjustable dumbbells',
            ],

            // Functional Training Equipment
            [
                'equipment_name' => 'Cable Crossover Machine',
                'equipment_type' => 'Functional Training',
                'brand' => 'FunctionalFit',
                'model' => 'CCM-400',
                'serial_number' => 'FF-CC-007',
                'purchase_date' => '2023-04-01',
                'purchase_price' => 3200.00,
                'warranty_expiry' => '2025-10-01',
                'last_maintenance_date' => '2024-05-20',
                'next_maintenance_date' => '2024-08-20',
                'maintenance_interval_days' => 90,
                'location' => 'Functional Training Zone',
                'status' => 'operational',
                'notes' => 'Versatile cable machine for functional movements',
            ],
            [
                'equipment_name' => 'TRX Suspension Trainer',
                'equipment_type' => 'Functional Training',
                'brand' => 'TRX',
                'model' => 'ST-PRO',
                'serial_number' => 'TRX-ST-008',
                'purchase_date' => '2023-04-10',
                'purchase_price' => 200.00,
                'warranty_expiry' => '2024-04-10',
                'last_maintenance_date' => '2024-04-10',
                'next_maintenance_date' => '2024-10-10',
                'maintenance_interval_days' => 180,
                'location' => 'Functional Training Zone',
                'status' => 'operational',
                'notes' => 'Bodyweight suspension training system',
            ],

            // Specialized Equipment
            [
                'equipment_name' => 'Rowing Machine Concept2',
                'equipment_type' => 'Cardio',
                'brand' => 'Concept2',
                'model' => 'Model D',
                'serial_number' => 'C2-RM-009',
                'purchase_date' => '2023-05-01',
                'purchase_price' => 900.00,
                'warranty_expiry' => '2025-05-01',
                'last_maintenance_date' => '2024-06-01',
                'next_maintenance_date' => '2024-09-01',
                'maintenance_interval_days' => 90,
                'location' => 'Cardio Zone B',
                'status' => 'operational',
                'notes' => 'Professional rowing machine used in competitions',
            ],
            [
                'equipment_name' => 'Battle Ropes Set',
                'equipment_type' => 'Functional Training',
                'brand' => 'CoreFit',
                'model' => 'BR-50',
                'serial_number' => 'CF-BR-010',
                'purchase_date' => '2023-05-15',
                'purchase_price' => 150.00,
                'warranty_expiry' => '2023-11-15',
                'last_maintenance_date' => '2024-05-15',
                'next_maintenance_date' => '2024-11-15',
                'maintenance_interval_days' => 180,
                'location' => 'Functional Training Zone',
                'status' => 'operational',
                'notes' => 'High-intensity interval training rope',
            ],

            // Recovery Equipment
            [
                'equipment_name' => 'Massage Chair Pro',
                'equipment_type' => 'Recovery',
                'brand' => 'RelaxMax',
                'model' => 'MCP-3000',
                'serial_number' => 'RM-MC-011',
                'purchase_date' => '2023-06-01',
                'purchase_price' => 4500.00,
                'warranty_expiry' => '2026-06-01',
                'last_maintenance_date' => '2024-06-01',
                'next_maintenance_date' => '2024-09-01',
                'maintenance_interval_days' => 90,
                'location' => 'Recovery Zone',
                'status' => 'operational',
                'notes' => 'Professional massage chair for member recovery',
            ],
            [
                'equipment_name' => 'Foam Roller Set',
                'equipment_type' => 'Recovery',
                'brand' => 'RecoveryPro',
                'model' => 'FRS-MULTI',
                'serial_number' => 'RP-FR-012',
                'purchase_date' => '2023-06-10',
                'purchase_price' => 300.00,
                'warranty_expiry' => '2024-06-10',
                'last_maintenance_date' => '2024-06-10',
                'next_maintenance_date' => '2024-12-10',
                'maintenance_interval_days' => 180,
                'location' => 'Recovery Zone',
                'status' => 'operational',
                'notes' => 'Complete foam rolling system for self-massage',
            ],

            // Equipment needing maintenance
            [
                'equipment_name' => 'Leg Press Machine',
                'equipment_type' => 'Strength Training',
                'brand' => 'LegPower',
                'model' => 'LP-800',
                'serial_number' => 'LP-LPM-013',
                'purchase_date' => '2022-12-01',
                'purchase_price' => 2800.00,
                'warranty_expiry' => '2024-12-01',
                'last_maintenance_date' => '2024-03-01',
                'next_maintenance_date' => '2024-07-01',
                'maintenance_interval_days' => 120,
                'location' => 'Strength Training Zone',
                'status' => 'under_maintenance',
                'notes' => 'Requires cable replacement and lubrication',
            ],
            [
                'equipment_name' => 'Smith Machine',
                'equipment_type' => 'Strength Training',
                'brand' => 'SafetyFirst',
                'model' => 'SM-1000',
                'serial_number' => 'SF-SM-014',
                'purchase_date' => '2022-11-15',
                'purchase_price' => 1800.00,
                'warranty_expiry' => '2024-05-15',
                'last_maintenance_date' => '2024-02-15',
                'next_maintenance_date' => '2024-08-15',
                'maintenance_interval_days' => 180,
                'location' => 'Strength Training Zone',
                'status' => 'out_of_service',
                'notes' => 'Safety mechanism malfunction - awaiting parts',
            ],
        ];

        foreach ($equipment as $equipmentData) {
            $purchaseDate = Carbon::parse($equipmentData['purchase_date']);
            $lastMaintenanceDate = isset($equipmentData['last_maintenance_date']) ? Carbon::parse($equipmentData['last_maintenance_date']) : null;
            $nextMaintenanceDate = isset($equipmentData['next_maintenance_date']) ? Carbon::parse($equipmentData['next_maintenance_date']) : null;
            $warrantyExpiry = isset($equipmentData['warranty_expiry']) ? Carbon::parse($equipmentData['warranty_expiry']) : null;
            
            $equipmentItem = Equipment::create([
                'equipment_name' => $equipmentData['equipment_name'],
                'equipment_type' => $equipmentData['equipment_type'],
                'brand' => $equipmentData['brand'],
                'model' => $equipmentData['model'],
                'serial_number' => $equipmentData['serial_number'],
                'purchase_date' => $purchaseDate,
                'purchase_price' => $equipmentData['purchase_price'],
                'warranty_expiry' => $warrantyExpiry,
                'last_maintenance_date' => $lastMaintenanceDate,
                'next_maintenance_date' => $nextMaintenanceDate,
                'maintenance_interval_days' => $equipmentData['maintenance_interval_days'],
                'location' => $equipmentData['location'],
                'status' => $equipmentData['status'],
                'notes' => $equipmentData['notes'],
                'created_at' => $purchaseDate,
                'updated_at' => now()->subDays(rand(0, 10)),
            ]);

            $this->command->info("Created equipment: {$equipmentItem->equipment_name} ({$equipmentItem->serial_number})");
        }

        $this->command->info('Equipment seeded successfully!');
        $this->command->info('Total equipment items created: ' . count($equipment));
        
        // Display summary by type and status
        $equipmentByType = Equipment::all()->groupBy('equipment_type');
        $this->command->info("\nEquipment by type:");
        foreach ($equipmentByType as $type => $typeEquipment) {
            $this->command->info("- {$type}: " . count($typeEquipment) . " items");
        }

        $equipmentByStatus = Equipment::all()->groupBy('status');
        $this->command->info("\nEquipment by status:");
        foreach ($equipmentByStatus as $status => $statusEquipment) {
            $this->command->info("- " . ucfirst(str_replace('_', ' ', $status)) . ": " . count($statusEquipment) . " items");
        }
    }
}