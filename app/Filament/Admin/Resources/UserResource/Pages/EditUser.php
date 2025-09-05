<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

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
                $this->record->assignRole($role);
            }
        }
    }
}