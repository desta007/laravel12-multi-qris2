<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Middleware\ApiAuthenticate;

// Test route for API
Route::get('/test-api', function () {
    return response()->json([
        'success' => true,
        'message' => 'QRIS Payment Gateway API is working!',
        'timestamp' => now()->toISOString()
    ]);
});

// Authentication routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// API routes
Route::middleware(ApiAuthenticate::class)->group(function () {
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/payment/generate-qris', [PaymentController::class, 'generateQris']);
    Route::get('/payment/transaction/{transactionId}', [PaymentController::class, 'getTransactionStatus']);
    Route::get('/payment/qris-list', [PaymentController::class, 'getQrisList']);
});

// Public callback route (no authentication)
Route::post('/payment/callback', [PaymentController::class, 'handleCallback']);

// Admin routes
Route::middleware([ApiAuthenticate::class, 'role:admin|master_admin'])->group(function () {
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');
});