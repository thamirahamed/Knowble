<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if the logged-in user is admin
        if ($user && $user->email === 'admin@apiit.lk') {
            return $next($request); // Allow access to the requested route
        }

        // Redirect non-admin users to the dashboard or an error page
        return redirect()->back();
    }
}
