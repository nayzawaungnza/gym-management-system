<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Authenticate Middleware
 *
 * Ensures the incoming request is authenticated using the specified guards.
 * Redirects unauthenticated users to the login page with an error message.
 * Includes logging for debugging and traceability.
 */
class Authenticate
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
                \Log::debug('AuthenticateMiddleware: User authenticated', [
                    'email' => $user->email,
                    'guard' => $guard ?: 'default',
                    'path' => $request->path(),
                    'roles' => $user->getRoleNames()->toJson(),
                    'session_id' => $request->session()->getId(),
                ]);
                return $next($request);
            }
        }

        \Log::warning('AuthenticateMiddleware: Unauthenticated access attempt', [
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('login')->with('error', 'Authentication required. Please log in.');
    }
}