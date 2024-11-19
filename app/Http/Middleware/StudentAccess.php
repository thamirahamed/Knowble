<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StudentAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Check if the authenticated user's email ends with @students.apiit.lk
        if ($user && str_ends_with($user->email, '@students.apiit.lk')) {
            return $next($request); // Allow access
        }

        // Redirect if the email does not match the domain
        return redirect()->back();

    }
}
