<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('User Information')
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
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->visible(fn ($livewire) => $livewire instanceof Pages\CreateUser),
                        
                        Forms\Components\TextInput::make('new_password')
                            ->password()
                            ->maxLength(255)
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->visible(fn ($livewire) => $livewire instanceof Pages\EditUser)
                            ->dehydrated(false)
                            ->label('New Password'),
                        
                        Forms\Components\Select::make('roles')
                            ->label('Role')
                            ->options(\Spatie\Permission\Models\Role::all()->pluck('name', 'id'))
                            ->required()
                            ->preload(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('roles'))
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
                    ->default('No Role')
                    ->formatStateUsing(fn ($state, $record) => $record->roles->first()?->name ?? 'No Role'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->options(\Spatie\Permission\Models\Role::all()->pluck('name', 'name'))
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
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