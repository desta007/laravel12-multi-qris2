<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // QRIS permissions
            'view-qris',
            'create-qris',
            'edit-qris',
            'delete-qris',
            
            // Transaction permissions
            'view-transactions',
            'create-transactions',
            'edit-transactions',
            'delete-transactions',
            
            // Member balance permissions
            'view-member-balances',
            'edit-member-balances',
            
            // Member permissions
            'view-members',
            'edit-members',
            
            // Report permissions
            'view-reports',
            'export-reports',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $masterAdminRole = Role::firstOrCreate(['name' => 'master_admin']);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $memberRole = Role::firstOrCreate(['name' => 'member']);

        // Assign permissions to roles
        // Master Admin has all permissions
        $masterAdminRole->givePermissionTo(Permission::all());

        // Admin permissions
        $adminPermissions = [
            'view-qris',
            'create-qris',
            'edit-qris',
            'view-transactions',
            'edit-transactions',
            'view-member-balances',
            'edit-member-balances',
            'view-members',
            'view-reports',
            'export-reports',
        ];
        $adminRole->givePermissionTo($adminPermissions);

        // Member permissions
        $memberPermissions = [
            'view-qris',
            'view-transactions',
            'view-member-balances',
        ];
        $memberRole->givePermissionTo($memberPermissions);
    }
}
