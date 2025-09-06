<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AdminResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'Admin Users';
    protected static ?string $pluralModelLabel = 'Admin Users';
    protected static ?string $modelLabel = 'Admin User';

    protected static ?int $navigationSort = 1;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->hasRole('master_admin');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Admin Information')
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
                            ->visible(fn($livewire) => $livewire instanceof Pages\CreateAdmin),

                        Forms\Components\TextInput::make('new_password')
                            ->password()
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn($state) => Hash::make($state))
                            ->visible(fn($livewire) => $livewire instanceof Pages\EditAdmin)
                            ->dehydrated(false)
                            ->label('New Password'),

                        Forms\Components\Select::make('roles')
                            ->label('Role')
                            ->options(function () {
                                $roles = \Spatie\Permission\Models\Role::whereIn('name', ['master_admin', 'admin']);

                                // If there's already a master_admin user, exclude the master_admin role option
                                if (\App\Models\User::role('master_admin')->exists()) {
                                    $roles->where('name', 'admin');
                                }

                                return $roles->pluck('name', 'id');
                            })
                            ->required()
                            ->preload(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->whereHas('roles', function ($query) {
                $query->whereIn('name', ['master_admin', 'admin']);
            })->with('roles'))
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn($state, $record) => $record->roles->first()?->name ?? 'No Role'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->options(function () {
                        $roles = \Spatie\Permission\Models\Role::whereIn('name', ['master_admin', 'admin']);

                        // If there's already a master_admin user, exclude the master_admin role option
                        if (\App\Models\User::role('master_admin')->exists()) {
                            $roles->where('name', 'admin');
                        }

                        return $roles->pluck('name', 'name');
                    })
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for admin users
            ]);
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
            'index' => Pages\ListAdmins::route('/'),
            'create' => Pages\CreateAdmin::route('/create'),
            'view' => Pages\ViewAdmin::route('/{record}'),
            'edit' => Pages\EditAdmin::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->hasRole('master_admin');
    }

    public static function canCreate(): bool
    {
        return Auth::user()->hasRole('master_admin');
    }

    public static function canEdit($record): bool
    {
        // Prevent editing master_admin user except by master_admin themselves
        if ($record->hasRole('master_admin') && Auth::user()->hasRole('master_admin')) {
            return true;
        }

        // Allow editing admin users
        if ($record->hasRole('admin') && Auth::user()->hasRole('master_admin')) {
            return true;
        }

        return false;
    }

    public static function canDelete($record): bool
    {
        // Prevent deletion of self
        if (Auth::id() === $record->id) {
            return false;
        }

        // Prevent deletion of master_admin
        if ($record->hasRole('master_admin')) {
            return false;
        }

        return Auth::user()->hasRole('master_admin');
    }
}
