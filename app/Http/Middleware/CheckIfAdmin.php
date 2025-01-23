<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckIfAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        // Check if the user is authenticated
        if (Auth::check()) {
            // Check if the authenticated user is an admin
            if (Auth::user()->role === 'admin') {
                return $next($request); // Allow the request to proceed
            }
        }

        // If the user is not an admin, return a forbidden response
        abort(403, 'Unauthorized action.');

    }
}
