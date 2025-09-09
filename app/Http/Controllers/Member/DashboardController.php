<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\MemberBalance;
use App\Models\Qris;
use Illuminate\Support\Facades\Cache;

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
            
        // Get active QRIS and implement rolling display
        $activeQris = Qris::where('is_active', true)->get();
        
        // Get one static and one dynamic QRIS for rolling display
        $staticQris = $this->getRollingQris($activeQris->where('type', 'static'));
        $dynamicQris = $this->getRollingQris($activeQris->where('type', 'dynamic'));
        
        return view('member.dashboard', compact('balance', 'recentTransactions', 'staticQris', 'dynamicQris'));
    }
    
    /**
     * Get a QRIS for rolling display based on cache
     */
    private function getRollingQris($qrisCollection)
    {
        if ($qrisCollection->isEmpty()) {
            return null;
        }
        
        // If only one QRIS, return it
        if ($qrisCollection->count() == 1) {
            return $qrisCollection->first();
        }
        
        // Use cache to implement rolling display
        $cacheKey = 'member_qris_rolling_' . auth()->id() . '_' . ($qrisCollection->first()->type ?? 'unknown');
        $lastIndex = Cache::get($cacheKey, -1);
        
        // Get next index
        $nextIndex = ($lastIndex + 1) % $qrisCollection->count();
        
        // Store the index for next time
        Cache::put($cacheKey, $nextIndex, now()->addMinutes(10));
        
        return $qrisCollection->values()->get($nextIndex);
    }
}
