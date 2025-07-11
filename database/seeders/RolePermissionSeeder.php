<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

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
            'export-members', 'export-attendance', 'export-payments'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and assign permissions
        $adminRole = Role::create(['name' => 'Admin']);
        $trainerRole = Role::create(['name' => 'Trainer']);
        $memberRole = Role::create(['name' => 'Member']);

        // Assign all permissions to Admin
        $adminRole->givePermissionTo(Permission::all());

        // Assign specific permissions to Trainer
        $trainerRole->givePermissionTo([
            'class-list', 'class-edit',
            'attendance-list', 'attendance-create', 'attendance-edit',
            'member-list',
            'dashboard-trainer',
            'report-attendance'
        ]);

        // Assign limited permissions to Member
        $memberRole->givePermissionTo([
            'class-list',
            'attendance-list',
            'dashboard-member'
        ]);

        // Create default users
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@gym.com',
            'password' => bcrypt('password'),
            'is_admin' => 1,
            'is_active' => true,
            'email_verified_at' => now()
        ]);
        $admin->assignRole('Admin');

        $trainer = User::create([
            'name' => 'Trainer User',
            'email' => 'trainer@gym.com',
            'password' => bcrypt('password'),
            'is_admin' => 2,
            'is_active' => true,
            'email_verified_at' => now()
        ]);
        $trainer->assignRole('Trainer');

        $member = User::create([
            'name' => 'Member User',
            'email' => 'member@gym.com',
            'password' => bcrypt('password'),
            'is_admin' => 0,
            'is_active' => true,
            'email_verified_at' => now()
        ]);
        $member->assignRole('Member');
    }
}