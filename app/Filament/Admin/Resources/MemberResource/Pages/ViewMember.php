<?php

namespace App\Filament\Admin\Resources\MemberResource\Pages;

use App\Filament\Admin\Resources\MemberResource;
use App\Models\MemberBalance;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ViewMember extends ViewRecord
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return 'Member Profile: ' . $this->record->name;
    }

    public function balanceTable(): Table
    {
        $balance = MemberBalance::firstOrCreate(
            ['user_id' => $this->record->id],
            ['balance' => 0, 'total_income' => 0, 'total_expense' => 0]
        );

        return Table::make()
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
            ])
            ->paginated(false)
            ->query(fn () => collect([$balance])->toQuery());
    }

    public function transactionsTable(): Table
    {
        return Table::make()
            ->columns([
                Tables\Columns\TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fee')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->searchable(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->query(fn () => Transaction::where('user_id', $this->record->id)->latest())
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10);
    }
}