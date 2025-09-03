<?php

namespace App\Filament\Member\Resources\TransactionResource\Pages;

use App\Filament\Member\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Form;
use Filament\Forms;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\TextInput::make('transaction_id')
                            ->label('Transaction ID')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('qris.name')
                            ->label('QRIS')
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('fee')
                            ->label('Fee')
                            ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                            ->disabled(),
                            
                        Forms\Components\TextInput::make('status')
                            ->disabled(),
                            
                        Forms\Components\DateTimePicker::make('paid_at')
                            ->label('Paid At')
                            ->disabled(),
                            
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Created At')
                            ->disabled(),
                            
                        Forms\Components\DateTimePicker::make('updated_at')
                            ->label('Updated At')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            // Add any actions here if needed
        ];
    }
}