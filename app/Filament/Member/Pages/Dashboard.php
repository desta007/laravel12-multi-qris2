<?php

namespace App\Filament\Member\Pages;

use Filament\Pages\Page;
use App\Models\Transaction;
use App\Models\MemberBalance;
use App\Models\Qris;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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

    public function getStaticQrisProperty()
    {
        $user = Auth::user();
        $activeQris = Qris::where('is_active', true)->where('type', 'static')->get();
        return $this->getRollingQris($activeQris);
    }

    public function getDynamicQrisProperty()
    {
        $user = Auth::user();
        $activeQris = Qris::where('is_active', true)->where('type', 'dynamic')->get();
        return $this->getRollingQris($activeQris);
    }
    
    /**
     * Generate QR code image from QRIS code
     */
    public function generateQrCodeImage($qris)
    {
        if (empty($qris?->qris_code)) {
            return null;
        }

        try {
            // Use SVG format which doesn't require Imagick
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(200)
                ->margin(2)
                ->generate($qris->qris_code);

            // Return as base64 encoded SVG data URL
            $encoded = base64_encode($qrCode);
            return 'data:image/svg+xml;base64,' . $encoded;
        } catch (\Exception $e) {
            \Log::error('QR Code generation failed: ' . $e->getMessage());
            return null;
        }
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
        $cacheKey = 'member_qris_rolling_' . Auth::id() . '_' . ($qrisCollection->first()->type ?? 'unknown');
        $lastIndex = Cache::get($cacheKey, -1);
        
        // Get next index
        $nextIndex = ($lastIndex + 1) % $qrisCollection->count();
        
        // Store the index for next time
        Cache::put($cacheKey, $nextIndex, now()->addMinutes(10));
        
        return $qrisCollection->values()->get($nextIndex);
    }
}