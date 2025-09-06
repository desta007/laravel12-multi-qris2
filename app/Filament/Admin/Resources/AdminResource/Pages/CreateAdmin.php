<?php

namespace App\Filament\Admin\Resources\AdminResource\Pages;

use App\Filament\Admin\Resources\AdminResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateAdmin extends CreateRecord
{
    protected static string $resource = AdminResource::class;

    protected function afterCreate(): void
    {
        // Assign the role after creating the user
        $roleId = $this->data['roles'] ?? null;
        if ($roleId) {
            $role = Role::find($roleId);
            if ($role) {
                // Check if trying to create another master_admin
                if ($role->name === 'master_admin') {
                    $existingMasterAdmin = Role::findByName('master_admin')->users()->first();
                    if ($existingMasterAdmin) {
                        // Remove master_admin role and assign admin role instead
                        $this->record->syncRoles([]);
                        $adminRole = Role::findByName('admin');
                        if ($adminRole) {
                            $this->record->assignRole($adminRole);
                        }
                        return;
                    }
                }
                $this->record->assignRole($role);
            }
        }
    }
}