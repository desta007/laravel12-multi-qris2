<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MemberBalance;
use App\Models\User;

class MemberBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users with member role
        $members = User::role('member')->get();
        
        foreach ($members as $member) {
            MemberBalance::firstOrCreate(
                ['user_id' => $member->id],
                [
                    'balance' => 0,
                    'total_income' => 0,
                    'total_expense' => 0
                ]
            );
        }
    }
}
