<?php

namespace App\Services;

use App\Models\Qris;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class QrisDistributionService
{
    protected static $roundRobinIndex = null;
    
    public static function getCurrentStrategy(): string
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
        
        // Default to config value or 'random'
        return config('qris.default_distribution_strategy', 'random');
    }
    
    public static function selectQris(array $activeQris): ?Qris
    {
        if (empty($activeQris)) {
            return null;
        }
        
        $strategy = self::getCurrentStrategy();
        
        switch ($strategy) {
            case 'random':
                return self::selectRandom($activeQris);
                
            case 'round_robin':
                return self::selectRoundRobin($activeQris);
                
            case 'manual':
                // For manual selection, we return null to indicate that manual selection is needed
                return null;
                
            default:
                // Default to random if unknown strategy
                return self::selectRandom($activeQris);
        }
    }
    
    protected static function selectRandom(array $activeQris): Qris
    {
        return $activeQris[array_rand($activeQris)];
    }
    
    protected static function selectRoundRobin(array $activeQris): Qris
    {
        // Get the round-robin index from cache or initialize it
        if (self::$roundRobinIndex === null) {
            self::$roundRobinIndex = Cache::get('round_robin_index', 0);
        }
        
        // Select the QRIS based on the current index
        $qris = $activeQris[self::$roundRobinIndex % count($activeQris)];
        
        // Update the index for next selection
        self::$roundRobinIndex = (self::$roundRobinIndex + 1) % count($activeQris);
        
        // Save the index to cache for persistence
        Cache::put('round_robin_index', self::$roundRobinIndex, 3600);
        
        return $qris;
    }
    
    public static function resetRoundRobinIndex(): void
    {
        self::$roundRobinIndex = null;
        Cache::forget('round_robin_index');
    }
}