<?php

namespace App\Filament\Admin\Resources\MemberBalanceResource\Pages;

use App\Filament\Admin\Resources\MemberBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMemberBalances extends ListRecords
{
    protected static string $resource = MemberBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
