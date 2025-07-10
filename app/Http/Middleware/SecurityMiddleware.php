<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SecurityEvent;
use App\Models\LoginHistory;

class SecurityMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Track login attempts and detect suspicious activity
        if ($request->routeIs('login') && $request->isMethod('post')) {
            $this->trackLoginAttempt($request);
        }
        
        // Check for account lockout
        if (auth()->check()) {
            $user = auth()->user();
            
            if ($user->locked_until && now()->lt($user->locked_until)) {
                auth()->logout();
                return redirect()->route('login')->withErrors([
                    'email' => 'Account is temporarily locked due to suspicious activity.'
                ]);
            }
        }
        
        return $next($request);
    }
    
    private function trackLoginAttempt(Request $request)
    {
        $email = $request->input('email');
        $ipAddress = $request->ip();
        
        // Check for brute force attempts
        $recentAttempts = LoginHistory::where('ip_address', $ipAddress)
            ->where('is_successful', false)
            ->where('login_at', '>=', now()->subMinutes(15))
            ->count();
        
        if ($recentAttempts >= 5) {
            SecurityEvent::create([
                'event_type' => 'suspicious_activity',
                'ip_address' => $ipAddress,
                'user_agent' => $request->userAgent(),
                'event_data' => [
                    'type' => 'brute_force_attempt',
                    'email' => $email,
                    'attempts' => $recentAttempts
                ],
                'risk_level' => 'high'
            ]);
        }
    }
}
