<?php

namespace App\Filament\Admin\Resources\MemberResource\Pages;

use App\Filament\Admin\Resources\MemberResource;
use Filament\Resources\Pages\CreateRecord;
use Spatie\Permission\Models\Role;

class CreateMember extends CreateRecord
{
    protected static string $resource = MemberResource::class;

    protected function afterCreate(): void
    {
        // Assign the member role to the newly created user
        $memberRole = Role::where('name', 'member')->first();
        if ($memberRole) {
            $this->record->assignRole($memberRole);
        }
    }
}