<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\MemberBalance;
use App\Models\Qris;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get member balance
        $balance = MemberBalance::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'total_income' => 0, 'total_expense' => 0]
        );
        
        // Get recent transactions
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
            
        // Get active QRIS
        $activeQris = Qris::where('is_active', true)->get();
        
        return view('member.dashboard', compact('balance', 'recentTransactions', 'activeQris'));
    }
}
