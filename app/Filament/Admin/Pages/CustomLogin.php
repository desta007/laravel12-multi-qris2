<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;

class CustomLogin extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        $response = parent::authenticate();
        
        // If authentication failed, return the response
        if (!$response) {
            return $response;
        }

        // Check if the authenticated user has the required roles
        $user = Auth::user();
        if ($user && !$user->hasAnyRole(['admin', 'master_admin'])) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'You do not have permission to access the admin panel.',
            ]);
        }

        // Return the parent response which handles the redirect
        return $response;
    }
}