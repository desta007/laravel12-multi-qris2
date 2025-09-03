<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Member\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs', function () {
    return view('docs');
});

// Authentication routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        auth()->logout();
        return redirect('/');
    })->name('logout');
});
