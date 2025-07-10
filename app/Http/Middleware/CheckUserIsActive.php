<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * CheckUserIsActive Middleware
 *
 * Ensures the authenticated user is active. Redirects inactive users to login with an error.
 */
class CheckUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Adjust 'is_active' based on your User model (e.g., 'status' or another field)
            if ($user->is_active ?? true) { // Default to true if field doesn't exist
                \Log::debug('CheckUserIsActive: User is active', ['email' => $user->email]);
                return $next($request);
            }

            \Log::warning('CheckUserIsActive: Inactive user attempted access', ['email' => $user->email]);
            Auth::logout();
            return redirect()->route('login')->with('error', 'Your account is inactive. Please contact support.');
        }

        \Log::warning('CheckUserIsActive: Unauthenticated user', ['path' => $request->path()]);
        return redirect()->route('login')->with('error', 'Authentication required.');
    }
}