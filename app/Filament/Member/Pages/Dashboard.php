<?php

namespace App\Filament\Member\Pages;

use Filament\Pages\Page;
use App\Models\Transaction;
use App\Models\MemberBalance;
use App\Models\Qris;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static ?int $navigationSort = -2;
    
    protected static ?string $title = 'Dashboard';

    protected static string $view = 'filament.member.pages.dashboard';

    public function getBalanceProperty()
    {
        $user = Auth::user();
        
        return MemberBalance::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'total_income' => 0, 'total_expense' => 0]
        );
    }

    public function getRecentTransactionsProperty()
    {
        $user = Auth::user();
        
        return Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function getActiveQrisProperty()
    {
        return Qris::where('is_active', true)->get();
    }
}