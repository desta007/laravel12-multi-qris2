<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        // Assign the role after creating the user
        $roleId = $this->data['roles'] ?? null;
        if ($roleId) {
            $role = Role::find($roleId);
            if ($role) {
                $this->record->assignRole($role);
            }
        }
    }
}