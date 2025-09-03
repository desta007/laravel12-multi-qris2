<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;
use App\Models\MemberBalance;
use Carbon\Carbon;

class ProcessPendingTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-pendings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending transactions and update balances';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get pending transactions older than 1 minute (to simulate processing)
        $transactions = Transaction::where('status', 'pending')
            ->where('created_at', '<', Carbon::now()->subMinute())
            ->get();
            
        if ($transactions->isEmpty()) {
            $this->info('No pending transactions to process');
            return 0;
        }
        
        $processedCount = 0;
        
        foreach ($transactions as $transaction) {
            // Randomly decide if payment is successful (80% success rate)
            $isSuccessful = rand(1, 100) <= 80;
            
            if ($isSuccessful) {
                // Update transaction status
                $transaction->update([
                    'status' => 'success',
                    'paid_at' => Carbon::now()
                ]);
                
                // Update member balance
                $memberBalance = MemberBalance::firstOrCreate(
                    ['user_id' => $transaction->user_id],
                    ['balance' => 0, 'total_income' => 0, 'total_expense' => 0]
                );
                
                $memberBalance->increment('balance', $transaction->amount);
                $memberBalance->increment('total_income', $transaction->amount);
                
                $this->info("Processed transaction {$transaction->transaction_id}: SUCCESS");
            } else {
                $transaction->update(['status' => 'failed']);
                $this->info("Processed transaction {$transaction->transaction_id}: FAILED");
            }
            
            $processedCount++;
        }
        
        $this->info("Processed {$processedCount} transactions");
        
        return 0;
    }
}
