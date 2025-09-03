<?php

namespace App\Filament\Admin\Resources\QrisResource\Pages;

use App\Filament\Admin\Resources\QrisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQris extends ListRecords
{
    protected static string $resource = QrisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
