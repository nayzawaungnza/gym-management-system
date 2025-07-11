<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * RedirectIfAuthenticated Middleware
 *
 * Redirects authenticated users to their role-specific dashboard.
 * Unauthenticated users are allowed to proceed to the requested route.
 */
class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     */
   public function handle(Request $request, Closure $next, ...$guards)
{
    $guards = empty($guards) ? [null] : $guards;

    foreach ($guards as $guard) {
        if (Auth::guard($guard)->check()) {
            $user = Auth::guard($guard)->user();
            
            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            // Clear intended URL to prevent loops
            redirect()->setIntendedUrl(null);
            
            // Let the controller handle the final redirect
            return $next($request);
        }
    }

    return $next($request);
}
}