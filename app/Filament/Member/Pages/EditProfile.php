<?php

namespace App\Filament\Member\Pages;

use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->required()
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Phone')
                            ->tel()
                            ->maxLength(20),
                    ])
                    ->columns(2),
                
                Section::make('Bank Information')
                    ->schema([
                        TextInput::make('bank_name')
                            ->label('Bank Name')
                            ->maxLength(255),
                        TextInput::make('account_holder_name')
                            ->label('Account Holder Name')
                            ->maxLength(255),
                        TextInput::make('account_number')
                            ->label('Account Number')
                            ->maxLength(50),
                    ])
                    ->columns(2),
            ]);
    }
    
    protected function getRedirectUrl(): string
    {
        return route('filament.member.pages.member-profile');
    }
}