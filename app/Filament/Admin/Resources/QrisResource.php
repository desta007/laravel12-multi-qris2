<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\QrisResource\Pages;
use App\Filament\Admin\Resources\QrisResource\RelationManagers;
use App\Models\Qris;
use App\Models\Bank;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class QrisResource extends Resource
{
    protected static ?string $model = Qris::class;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';

    public static function canViewAny(): bool
    {
        return Auth::user()->hasAnyRole(['master_admin', 'admin']);
    }

    public static function canCreate(): bool
    {
        return Auth::user()->hasAnyRole(['master_admin', 'admin']);
    }

    public static function canEdit($record): bool
    {
        return Auth::user()->hasAnyRole(['master_admin', 'admin']);
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
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('bank_id')
                    ->label('Bank')
                    ->relationship('bank', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('code')
                            ->required()
                            ->unique()
                            ->maxLength(50),
                    ])
                    ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                        return $action
                            ->hidden(!Auth::user()->hasRole('master_admin'));
                    }),
                Forms\Components\Textarea::make('qris_code')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('qris_image')
                    ->image(),
                Forms\Components\Select::make('type')
                    ->options([
                        'static' => 'Static',
                        'dynamic' => 'Dynamic',
                    ])
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                Forms\Components\TextInput::make('fee_percentage')
                    ->numeric()
                    ->suffix('%')
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bank.name')
                    ->label('Bank')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('fee_percentage')
                    ->suffix('%'),
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
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListQris::route('/'),
            'create' => Pages\CreateQris::route('/create'),
            'edit' => Pages\EditQris::route('/{record}/edit'),
        ];
    }
}
