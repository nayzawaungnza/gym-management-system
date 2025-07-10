<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Create additional permissions for roles and activity logs
        $newPermissions = [
            // Role permissions
            'role-list', 'role-create', 'role-edit', 'role-delete',
            // Permission permissions
            'permission-list', 'permission-create', 'permission-edit', 'permission-delete',
            // Activity log permissions
            'activity-list', 'activity-export', 'activity-cleanup',
            
        ];

        foreach ($newPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign new permissions to Admin role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($newPermissions);
        }

        // Add export permissions
        $exportPermissions = [
            'export-data',
            'export-members', 
            'export-attendance',
            'export-reports'
        ];

        foreach ($exportPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign export permissions to Admin role
        if ($adminRole) {
            $adminRole->givePermissionTo($exportPermissions);
        }
    }
}