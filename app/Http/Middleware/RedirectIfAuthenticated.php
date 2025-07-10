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
                $roles = $user->getRoleNames()->toJson();
                \Log::info('RedirectIfAuthenticated: Authenticated user redirected', [
                    'email' => $user->email,
                    'roles' => $roles,
                    'guard' => $guard ?: 'default',
                    'path' => $request->path(),
                ]);

                // Check email verification
                if (!$user->hasVerifiedEmail()) {
                    \Log::info('RedirectIfAuthenticated: User email not verified', ['email' => $user->email]);
                    return redirect()->route('verification.notice');
                }

                // Redirect based on role
                if ($user->hasRole('Admin')) {
                    return redirect()->intended('/admin/dashboard');
                } elseif ($user->hasRole('Trainer')) {
                    return redirect()->intended('/trainer/dashboard');
                } else {
                    return redirect()->intended('/dashboard');
                }
            }
        }

        \Log::debug('RedirectIfAuthenticated: Unauthenticated user proceeding', [
            'path' => $request->path(),
            'ip' => $request->ip(),
        ]);

        return $next($request);
    }
}