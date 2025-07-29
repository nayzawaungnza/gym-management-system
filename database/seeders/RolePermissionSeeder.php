<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // Member permissions
            'member-list', 'member-create', 'member-edit', 'member-delete', 'member-export',
            // Trainer permissions
            'trainer-list', 'trainer-create', 'trainer-edit', 'trainer-delete',
            // Class permissions
            'class-list', 'class-create', 'class-edit', 'class-delete',
            // Payment permissions
            'payment-list', 'payment-create', 'payment-edit', 'payment-delete',
            // Equipment permissions
            'equipment-list', 'equipment-create', 'equipment-edit', 'equipment-delete',
            // Attendance permissions
            'attendance-list', 'attendance-create', 'attendance-edit', 'attendance-export',
            // Activity log permissions
            'activity-list', 'activity-export', 'activity-cleanup',
            // Role permissions
            'role-list', 'role-create', 'role-edit', 'role-delete',
            // Report permissions
            'report-membership', 'report-financial', 'report-attendance',
            // Dashboard permissions
            'dashboard-admin', 'dashboard-trainer', 'dashboard-member',
            // Export permissions
            'export-members', 'export-attendance', 'export-payments',
            // User permissions
            'user-list', 'user-create', 'user-edit', 'user-delete',
            // Additional permissions
            'settings-manage', 'notifications-send'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $trainerRole = Role::firstOrCreate(['name' => 'Trainer']);
        $memberRole = Role::firstOrCreate(['name' => 'Member']);

        // Assign all permissions to Admin
        $adminRole->syncPermissions(Permission::all());

        // Assign specific permissions to Trainer
        $trainerPermissions = [
            'class-list', 'class-edit',
            'attendance-list', 'attendance-create', 'attendance-edit',
            'member-list',
            'dashboard-trainer',
            'report-attendance',
            'equipment-list'
        ];
        $trainerRole->syncPermissions($trainerPermissions);

        // Assign limited permissions to Member
        $memberPermissions = [
            'class-list',
            'attendance-list',
            'dashboard-member',
            'equipment-list'
        ];
        $memberRole->syncPermissions($memberPermissions);

        $this->command->info('Roles and permissions seeded successfully!');
        $this->command->info('Admin permissions: ' . $adminRole->permissions->count());
        $this->command->info('Trainer permissions: ' . count($trainerPermissions));
        $this->command->info('Member permissions: ' . count($memberPermissions));
    }
}