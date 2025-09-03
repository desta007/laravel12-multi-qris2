<?php

return [
    /*
    |--------------------------------------------------------------------------
    | QRIS Payment Gateway Settings
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the QRIS payment gateway.
    |
    */
    
    'default_distribution_strategy' => env('QRIS_DEFAULT_DISTRIBUTION_STRATEGY', 'random'),
    
    'transaction_expiration_minutes' => env('QRIS_TRANSACTION_EXPIRATION_MINUTES', 15),
    
    'callback_timeout_seconds' => env('QRIS_CALLBACK_TIMEOUT_SECONDS', 30),
    
    'qris_types' => [
        'static' => 'Static QRIS',
        'dynamic' => 'Dynamic QRIS'
    ],
    
    'fee_calculation' => [
        'default_percentage' => 0.7,
        'max_percentage' => 1.0,
        'min_percentage' => 0.1
    ],
    
    'export_formats' => [
        'csv',
        'xlsx'
    ]
];