<?php

namespace App\Filament\Member\Pages;

use Filament\Pages\Page;

class MemberProfile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static string $view = 'filament.member.pages.member-profile';

    public function getTitle(): string
    {
        return 'Profile';
    }
}