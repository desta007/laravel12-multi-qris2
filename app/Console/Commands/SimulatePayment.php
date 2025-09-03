<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Qris;

class SimulatePayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:simulate-payment {user?} {amount?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate a payment transaction for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get user
        $userId = $this->argument('user');
        if ($userId) {
            $user = User::find($userId);
        } else {
            $user = User::role('member')->inRandomOrder()->first();
        }
        
        if (!$user) {
            $this->error('No member user found');
            return 1;
        }
        
        // Get amount
        $amount = $this->argument('amount') ?? rand(10000, 1000000);
        
        // Get random QRIS
        $qris = Qris::where('is_active', true)->inRandomOrder()->first();
        
        if (!$qris) {
            $this->error('No active QRIS found');
            return 1;
        }
        
        // Calculate fee
        $fee = $amount * ($qris->fee_percentage / 100);
        
        // Create transaction
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'qris_id' => $qris->id,
            'transaction_id' => 'TRX-' . strtoupper(\Illuminate\Support\Str::random(10)),
            'amount' => $amount,
            'fee' => $fee,
            'status' => 'pending',
            'description' => 'Simulated payment for testing',
        ]);
        
        $this->info("Created transaction: {$transaction->transaction_id}");
        $this->info("User: {$user->name}");
        $this->info("Amount: Rp " . number_format($amount, 0, ',', '.'));
        $this->info("Fee: Rp " . number_format($fee, 0, ',', '.'));
        $this->info("QRIS: {$qris->name}");
        
        return 0;
    }
}
