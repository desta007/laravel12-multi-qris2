<?php

namespace App\Http\Controllers;

use App\Models\Qris;
use App\Services\QrisDistributionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function createTransaction(Request $request)
    {
        // Get all active QRIS
        $activeQris = Qris::where('is_active', true)->get();
        
        if ($activeQris->isEmpty()) {
            return response()->json(['error' => 'No active QRIS available'], 400);
        }
        
        // Get the selected QRIS based on distribution strategy
        $selectedQris = QrisDistributionService::selectQris($activeQris->toArray());
        
        // If manual selection is required, return the list of QRIS for selection
        if ($selectedQris === null) {
            return response()->json([
                'message' => 'Manual QRIS selection required',
                'qris_list' => $activeQris
            ]);
        }
        
        // Proceed with transaction creation using the selected QRIS
        // ... (your transaction creation logic here)
        
        return response()->json([
            'message' => 'Transaction created successfully',
            'qris' => $selectedQris
        ]);
    }
}