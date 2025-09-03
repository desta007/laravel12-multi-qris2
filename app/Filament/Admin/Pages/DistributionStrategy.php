<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Filament\Notifications\Notification;

class DistributionStrategy extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.admin.pages.distribution-strategy';

    public string $strategy = 'random';
    
    public array $strategies = [
        'random' => 'Random Selection',
        'round-robin' => 'Round Robin Distribution',
        'manual' => 'Manual QRIS Selection',
    ];

    public function mount(): void
    {
        // Load the current strategy from storage or use default
        $this->strategy = $this->getCurrentStrategy();
    }

    public function getCurrentStrategy(): string
    {
        // First check cache
        $strategy = Cache::get('distribution_strategy');
        
        if ($strategy) {
            return $strategy;
        }
        
        // Then check file storage
        $storagePath = storage_path('app/distribution_strategy.txt');
        if (File::exists($storagePath)) {
            $strategy = trim(File::get($storagePath));
            // Cache for 1 hour
            Cache::put('distribution_strategy', $strategy, 3600);
            return $strategy;
        }
        
        // Default to random
        return 'random';
    }

    public function save(): void
    {
        // Save to file storage
        $storagePath = storage_path('app/distribution_strategy.txt');
        File::put($storagePath, $this->strategy);
        
        // Update cache
        Cache::put('distribution_strategy', $this->strategy, 3600);
        
        // Show success notification
        Notification::make()
            ->title('Distribution strategy updated successfully!')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('master_admin');
    }
}