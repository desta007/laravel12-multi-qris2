<?php

namespace App\Filament\Member\Pages;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Facades\Filament;

class MemberLogin extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();
        
        // If authentication failed, return the response
        if (!$response) {
            return $response;
        }

        // Check if the authenticated user has admin roles
        $user = Auth::user();
        if ($user && $user->hasAnyRole(['admin', 'master_admin'])) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Administrators cannot access the member panel. Please use the admin panel instead.',
            ]);
        }

        // Return the parent response which handles the redirect
        return $response;
    }
}