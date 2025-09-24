<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Qris;
use App\Models\Bank;

class QrisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First ensure banks exist
        $this->call(BankSeeder::class);
        
        $qrisEntries = [
            [
                'name' => 'BCA Static QRIS',
                'bank_id' => Bank::where('name', 'BCA')->first()->id,
                'qris_code' => 'DUMMY_QRIS_CODE_BCA_STATIC_001',
                'type' => 'static',
                'is_active' => true,
                'fee_percentage' => 0.7,
            ],
            [
                'name' => 'BCA Dynamic QRIS',
                'bank_id' => Bank::where('name', 'BCA')->first()->id,
                'qris_code' => 'DUMMY_QRIS_CODE_BCA_DYNAMIC_001',
                'type' => 'dynamic',
                'is_active' => true,
                'fee_percentage' => 0.7,
                'bca_merchant_id' => 'MIDBCA001',
                'bca_terminal_id' => 'TIDBCA001',
            ],
            [
                'name' => 'Mandiri Dynamic QRIS',
                'bank_id' => Bank::where('name', 'Mandiri')->first()->id,
                'qris_code' => 'DUMMY_QRIS_CODE_MANDIRI_DYNAMIC_001',
                'type' => 'dynamic',
                'is_active' => true,
                'fee_percentage' => 0.8,
            ],
            [
                'name' => 'BNI Static QRIS',
                'bank_id' => Bank::where('name', 'BNI')->first()->id,
                'qris_code' => 'DUMMY_QRIS_CODE_BNI_STATIC_001',
                'type' => 'static',
                'is_active' => true,
                'fee_percentage' => 0.6,
            ],
            [
                'name' => 'BRI Dynamic QRIS',
                'bank_id' => Bank::where('name', 'BRI')->first()->id,
                'qris_code' => 'DUMMY_QRIS_CODE_BRI_DYNAMIC_001',
                'type' => 'dynamic',
                'is_active' => true,
                'fee_percentage' => 0.75,
            ],
        ];

        foreach ($qrisEntries as $qris) {
            Qris::firstOrCreate(
                ['qris_code' => $qris['qris_code']],
                $qris
            );
        }
    }
}
