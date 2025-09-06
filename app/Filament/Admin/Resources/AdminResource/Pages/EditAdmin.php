<?php

namespace App\Filament\Admin\Resources\AdminResource\Pages;

use App\Filament\Admin\Resources\AdminResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EditAdmin extends EditRecord
{
    protected static string $resource = AdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['new_password'])) {
            $data['password'] = Hash::make($data['new_password']);
        } else {
            unset($data['new_password']);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Sync the role after saving the user
        $roleId = $this->data['roles'] ?? null;
        if ($roleId) {
            // First remove all roles
            $this->record->syncRoles([]);
            // Then assign the new role
            $role = Role::find($roleId);
            if ($role) {
                // Check if trying to assign master_admin role
                if ($role->name === 'master_admin') {
                    $existingMasterAdmin = Role::findByName('master_admin')->users()->first();
                    // If there's already a master_admin and it's not this user, assign admin role instead
                    if ($existingMasterAdmin && $existingMasterAdmin->id !== $this->record->id) {
                        $adminRole = Role::findByName('admin');
                        if ($adminRole) {
                            $this->record->assignRole($adminRole);
                        }
                    } else {
                        $this->record->assignRole($role);
                    }
                } else {
                    $this->record->assignRole($role);
                }
            }
        }
    }
}