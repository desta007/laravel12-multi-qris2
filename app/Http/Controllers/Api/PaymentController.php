<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Qris;
use App\Models\Transaction;
use App\Models\MemberBalance;
use App\Services\QrisDistributionService;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $qrisDistributionService;
    
    public function __construct(QrisDistributionService $qrisDistributionService)
    {
        $this->qrisDistributionService = $qrisDistributionService;
    }
    
    /**
     * Generate a new payment QRIS
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function generateQris(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'description' => 'nullable|string|max:255',
            'callback_url' => 'nullable|url',
            'distribution_strategy' => 'nullable|in:random,round_robin,specific',
            'qris_id' => 'nullable|exists:qris,id'
        ]);

        // Get all active QRIS
        $activeQris = Qris::where('is_active', true)->get();
        
        if ($activeQris->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No active QRIS available'
            ], 404);
        }
        
        // Get QRIS based on distribution strategy
        $strategy = $request->input('distribution_strategy', config('qris.default_distribution_strategy', 'random'));
        $specificQrisId = $request->input('qris_id');
        
        // Get the selected QRIS based on distribution strategy
        $selectedQris = null;
        
        if ($strategy === 'specific' && $specificQrisId) {
            // For specific strategy, get the specified QRIS
            $selectedQris = $activeQris->firstWhere('id', $specificQrisId);
            
            if (!$selectedQris) {
                return response()->json([
                    'success' => false,
                    'message' => 'Specified QRIS not found or not active'
                ], 404);
            }
        } else {
            // For other strategies, use the distribution service
            $selectedQris = QrisDistributionService::selectQris($activeQris->all());
            
            if (!$selectedQris) {
                // Manual selection is required
                return response()->json([
                    'success' => false,
                    'message' => 'Manual QRIS selection required'
                ], 400);
            }
        }
        
        // Calculate fee
        $fee = $request->amount * ($selectedQris->fee_percentage / 100);
        
        // Create transaction
        $transaction = Transaction::create([
            'user_id' => auth()->id(),
            'qris_id' => $selectedQris->id,
            'transaction_id' => 'TRX-' . strtoupper(Str::random(10)),
            'amount' => $request->amount,
            'fee' => $fee,
            'status' => 'pending',
            'description' => $request->description,
            'callback_url' => $request->callback_url,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'transaction_id' => $transaction->transaction_id,
                'amount' => $transaction->amount,
                'fee' => $transaction->fee,
                'total_amount' => $transaction->amount + $transaction->fee,
                'qris_code' => $selectedQris->qris_code,
                'qris_image' => $selectedQris->qris_image,
                'bank_name' => $selectedQris->bank->name,
                'expires_at' => now()->addMinutes(15), // QRIS expires in 15 minutes
            ]
        ]);
    }

    /**
     * Get transaction status
     *
     * @param string $transactionId
     * @return JsonResponse
     */
    public function getTransactionStatus(string $transactionId): JsonResponse
    {
        $transaction = Transaction::where('transaction_id', $transactionId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'transaction_id' => $transaction->transaction_id,
                'amount' => $transaction->amount,
                'fee' => $transaction->fee,
                'status' => $transaction->status,
                'description' => $transaction->description,
                'paid_at' => $transaction->paid_at,
                'created_at' => $transaction->created_at,
            ]
        ]);
    }

    /**
     * Get member's QRIS list
     *
     * @return JsonResponse
     */
    public function getQrisList(): JsonResponse
    {
        $qrisList = Qris::with('bank')->where('is_active', true)
            ->select('id', 'name', 'bank_id', 'type', 'qris_image')
            ->get()
            ->map(function ($qris) {
                return [
                    'id' => $qris->id,
                    'name' => $qris->name,
                    'bank_name' => $qris->bank->name,
                    'type' => $qris->type,
                    'qris_image' => $qris->qris_image
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $qrisList
        ]);
    }

    /**
     * Callback handler for payment notifications
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handleCallback(Request $request): JsonResponse
    {
        // In a real implementation, you would verify the callback source
        // For now, we'll simulate a successful payment
        
        $transactionId = $request->input('transaction_id');
        
        $transaction = Transaction::where('transaction_id', $transactionId)->first();
        
        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found'
            ], 404);
        }
        
        // Update transaction status
        $transaction->update([
            'status' => 'success',
            'paid_at' => now(),
            'callback_response' => json_encode($request->all())
        ]);
        
        // Update member balance
        $memberBalance = MemberBalance::firstOrCreate(
            ['user_id' => $transaction->user_id],
            ['balance' => 0, 'total_income' => 0, 'total_expense' => 0]
        );
        
        $memberBalance->increment('balance', $transaction->amount);
        $memberBalance->increment('total_income', $transaction->amount);
        
        // Send callback to merchant if URL is provided
        if ($transaction->callback_url) {
            // In a real implementation, you would send an HTTP request to the callback URL
            // For now, we'll just log it
            \Log::info('Callback sent to: ' . $transaction->callback_url);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Payment processed successfully'
        ]);
    }
}
