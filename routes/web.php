<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs', function () {
    return view('docs');
});

// Redirect /member to /member/dashboard (with trailing slash)
Route::get('/member/', function () {
    return redirect('/member/dashboard');
});

// Authentication routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        auth()->logout();
        return redirect('/');
    })->name('logout');
});
