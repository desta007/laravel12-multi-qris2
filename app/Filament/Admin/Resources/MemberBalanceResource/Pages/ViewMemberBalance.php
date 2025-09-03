<?php

namespace App\Filament\Admin\Resources\MemberBalanceResource\Pages;

use App\Filament\Admin\Resources\MemberBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMemberBalance extends ViewRecord
{
    protected static string $resource = MemberBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No actions for view page
        ];
    }
}