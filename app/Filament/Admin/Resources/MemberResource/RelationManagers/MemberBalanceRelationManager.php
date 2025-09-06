<?php

namespace App\Filament\Admin\Resources\MemberResource\RelationManagers;

use App\Models\MemberBalance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class MemberBalanceRelationManager extends RelationManager
{
    protected static string $relationship = 'memberBalance';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('balance')
                    ->money('IDR')
                    ->label('Current Balance'),
                Tables\Columns\TextColumn::make('total_income')
                    ->money('IDR')
                    ->label('Total Income'),
                Tables\Columns\TextColumn::make('total_expense')
                    ->money('IDR')
                    ->label('Total Expense'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                // No create action for member balance
            ])
            ->actions([
                // No actions
            ])
            ->bulkActions([
                // No bulk actions
            ]);
    }
}