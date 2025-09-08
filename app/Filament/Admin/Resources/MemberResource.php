<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\MemberResource\Pages;
use App\Filament\Admin\Resources\MemberResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class MemberResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'Member Users';
    protected static ?string $pluralModelLabel = 'Member Users';
    protected static ?string $modelLabel = 'Member User';

    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        // Menampilkan menu User Management untuk role 'admin' dan 'master_admin'
        return Auth::user()->hasAnyRole(['master_admin', 'admin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Member Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->required()
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->visible(fn($livewire) => $livewire instanceof Pages\CreateMember),

                        Forms\Components\TextInput::make('new_password')
                            ->password()
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->visible(fn($livewire) => $livewire instanceof Pages\EditMember)
                            ->dehydrated(false)
                            ->label('New Password'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->whereHas('roles', function ($query) {
                $query->where('name', 'member');
            })->with('roles'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for member users
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MemberBalanceRelationManager::class,
            RelationManagers\TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'view' => Pages\ViewMember::route('/{record}'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        // Mengizinkan role 'admin' dan 'master_admin' untuk melihat Member Users
        return Auth::user()->hasAnyRole(['master_admin', 'admin']);
    }

    public static function canCreate(): bool
    {
        // Mengizinkan role 'admin' dan 'master_admin' untuk membuat Member Users
        return Auth::user()->hasAnyRole(['master_admin', 'admin']);
    }

    public static function canEdit($record): bool
    {
        return Auth::user()->hasRole('master_admin');
    }

    public static function canDelete($record): bool
    {
        // Prevent deletion of self
        if (Auth::id() === $record->id) {
            return false;
        }

        return Auth::user()->hasRole('master_admin');
    }
}
