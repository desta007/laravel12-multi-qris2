<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Qris;
use App\Models\MemberBalance;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the role and permission seeder first
        $this->call(RolePermissionSeeder::class);
        
        // Run the bank seeder
        $this->call(BankSeeder::class);
        
        // Run the QRIS seeder
        $this->call(QrisSeeder::class);
        
        // Create sample users with roles
        $masterAdmin = User::factory()->create([
            'name' => 'Master Admin',
            'email' => 'master@example.com',
        ]);
        $masterAdmin->assignRole('master_admin');
        
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);
        $admin->assignRole('admin');
        
        $member = User::factory()->create([
            'name' => 'Member',
            'email' => 'member@example.com',
        ]);
        $member->assignRole('member');
        
        // Create a test user
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        // Run the member balance seeder
        $this->call(MemberBalanceSeeder::class);
        
        // Create sample transactions
        $qrisList = Qris::all();
        $members = User::role('member')->get();
        
        foreach (range(1, 20) as $index) {
            $member = $members->random();
            $qris = $qrisList->random();
            $amount = rand(10000, 1000000);
            $fee = $amount * ($qris->fee_percentage / 100);
            
            $transaction = Transaction::create([
                'user_id' => $member->id,
                'qris_id' => $qris->id,
                'transaction_id' => 'TRX-' . strtoupper(Str::random(10)),
                'amount' => $amount,
                'fee' => $fee,
                'status' => ['pending', 'success', 'failed'][array_rand(['pending', 'success', 'failed'])],
                'description' => 'Sample transaction #' . $index,
                'paid_at' => now(),
                'created_at' => now()->subDays(rand(1, 30)),
                'updated_at' => now()->subDays(rand(1, 30)),
            ]);
            
            // Update member balance if transaction is successful
            if ($transaction->status === 'success') {
                $memberBalance = MemberBalance::firstOrCreate(
                    ['user_id' => $member->id],
                    ['balance' => 0, 'total_income' => 0, 'total_expense' => 0]
                );
                
                $memberBalance->increment('balance', $amount);
                $memberBalance->increment('total_income', $amount);
            }
        }
    }
}
