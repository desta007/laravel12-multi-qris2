<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MemberRedirectMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the request is for /member exactly (without trailing segments)
        if ($request->path() === 'member' || $request->path() === 'member/') {
            return redirect('/member/dashboard');
        }
        
        return $next($request);
    }
}
