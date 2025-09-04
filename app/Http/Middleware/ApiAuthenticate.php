<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if this is an API request
        if ($request->is('api/*')) {
            // For API requests, we want to return JSON responses for auth failures
            try {
                // Use the default auth middleware but catch any authentication exceptions
                $response = app(\Illuminate\Auth\Middleware\Authenticate::class)->handle($request, function ($request) use ($next) {
                    return $next($request);
                }, 'sanctum');
                
                return $response;
            } catch (AuthenticationException $e) {
                // Return JSON response for API authentication failures
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }
        }
        
        // For non-API requests, use the default auth middleware
        return app(\Illuminate\Auth\Middleware\Authenticate::class)->handle($request, $next, 'sanctum');
    }
}