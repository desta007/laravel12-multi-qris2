<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bank;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banks = [
            [
                'name' => 'BCA',
                'code' => 'bca',
                'is_active' => true
            ],
            [
                'name' => 'Mandiri',
                'code' => 'mandiri',
                'is_active' => true
            ],
            [
                'name' => 'BNI',
                'code' => 'bni',
                'is_active' => true
            ],
            [
                'name' => 'BRI',
                'code' => 'bri',
                'is_active' => true
            ],
            [
                'name' => 'CIMB Niaga',
                'code' => 'cimb',
                'is_active' => true
            ],
            [
                'name' => 'Danamon',
                'code' => 'danamon',
                'is_active' => true
            ],
            [
                'name' => 'Maybank',
                'code' => 'maybank',
                'is_active' => true
            ],
            [
                'name' => 'Permata',
                'code' => 'permata',
                'is_active' => true
            ],
            [
                'name' => 'BTN',
                'code' => 'btn',
                'is_active' => true
            ],
            [
                'name' => 'BTPN',
                'code' => 'btpn',
                'is_active' => true
            ],
            [
                'name' => 'OCBC NISP',
                'code' => 'ocbc',
                'is_active' => true
            ],
            [
                'name' => 'UOB',
                'code' => 'uob',
                'is_active' => true
            ],
            [
                'name' => 'HSBC',
                'code' => 'hsbc',
                'is_active' => true
            ],
            [
                'name' => 'Standard Chartered',
                'code' => 'standard-chartered',
                'is_active' => true
            ],
            [
                'name' => 'Citibank',
                'code' => 'citibank',
                'is_active' => true
            ],
            [
                'name' => 'ANZ',
                'code' => 'anz',
                'is_active' => true
            ],
            [
                'name' => 'Bank Jatim',
                'code' => 'jatim',
                'is_active' => true
            ],
            [
                'name' => 'Bank Jateng',
                'code' => 'jateng',
                'is_active' => true
            ],
            [
                'name' => 'Bank Jabar Banten',
                'code' => 'bjb',
                'is_active' => true
            ],
            [
                'name' => 'Bank DKI',
                'code' => 'dki',
                'is_active' => true
            ]
        ];

        foreach ($banks as $bank) {
            Bank::firstOrCreate(
                ['code' => $bank['code']],
                $bank
            );
        }
    }
}
