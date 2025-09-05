<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MemberBalanceResource\Pages;
use App\Filament\Admin\Resources\MemberBalanceResource\RelationManagers;
use App\Models\MemberBalance;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class MemberBalanceResource extends Resource
{
    protected static ?string $model = MemberBalance::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    // Hide from navigation
    protected static bool $shouldRegisterNavigation = false;

    public static function canViewAny(): bool
    {
        return Auth::user()->hasAnyRole(['master_admin', 'admin']);
    }

    public static function canCreate(): bool
    {
        return Auth::user()->hasRole('master_admin');
    }

    public static function canEdit($record): bool
    {
        return Auth::user()->hasRole('master_admin');
    }

    public static function canDelete($record): bool
    {
        return Auth::user()->hasRole('master_admin');
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()->hasRole('master_admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('balance')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('total_income')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\TextInput::make('total_expense')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_income')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_expense')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Auth::user()->hasRole('master_admin') 
                ? Tables\Actions\EditAction::make()
                : Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions(
                Auth::user()->hasRole('master_admin') 
                ? [
                    Tables\Actions\BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                    ]),
                ]
                : []
            );
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMemberBalances::route('/'),
            'create' => Pages\CreateMemberBalance::route('/create'),
            'edit' => Pages\EditMemberBalance::route('/{record}/edit'),
            'view' => Pages\ViewMemberBalance::route('/{record}'),
        ];
    }
}
