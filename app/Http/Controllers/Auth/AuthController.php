<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Member;
use App\Models\Trainer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\ThrottlesLogins;

class AuthController extends Controller
{
    use ThrottlesLogins;

    // Maximum login attempts before throttling
    protected $maxAttempts = 5;
    // Minutes to lock out after max attempts
    protected $decayMinutes = 15;

    public function showLoginForm()
    {
        Log::debug('Showing login form', [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // Check for too many login attempts
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->handleSuccessfulLogin($request);
        }

        $this->incrementLoginAttempts($request);
        return $this->sendFailedLoginResponse($request);
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,dns',
            'password' => 'required|string|min:8',
        ], [
            'email.required' => 'Please enter your email address',
            'email.email' => 'Please enter a valid email address',
            'password.required' => 'Please enter your password',
            'password.min' => 'Password must be at least 8 characters',
        ]);
    }

    protected function attemptLogin(Request $request)
    {
        return Auth::attempt(
            $request->only('email', 'password'),
            $request->filled('remember')
        );
    }

    protected function handleSuccessfulLogin(Request $request)
    {
        $request->session()->regenerate();
        $this->clearLoginAttempts($request);

        $user = Auth::user();
        $this->logSuccessfulLogin($user);

        if (!$this->isAccountActive($user)) {
            return $this->handleInactiveAccount($request, $user);
        }

        if (!$user->hasVerifiedEmail()) {
            return $this->handleUnverifiedEmail($user);
        }

        if ($this->requiresTwoFactorAuth($user)) {
            return $this->handleTwoFactorAuth($user);
        }

        return $this->redirectBasedOnRole($user);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    protected function logSuccessfulLogin($user)
    {
        try {
            saveActivityLog([
                'subject' => $user,
                'event' => 'login',
                'description' => sprintf('User %s logged in successfully', $user->email),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        } catch (\Exception $e) {
            Log::error('Activity log error: '.$e->getMessage());
        }
    }

    protected function isAccountActive($user)
    {
        return $user->is_active ?? false;
    }

    protected function handleInactiveAccount(Request $request, $user)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        Log::warning('Inactive account login attempt', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);
        
        return redirect()->route('login')
            ->with('error', 'Your account is inactive. Please contact support.');
    }

    protected function handleUnverifiedEmail($user)
    {
        Log::notice('Unverified email access attempt', ['user_id' => $user->id]);
        return redirect()->route('verification.notice');
    }

    protected function requiresTwoFactorAuth($user)
    {
        return $user->two_factor_secret !== null;
    }

    protected function handleTwoFactorAuth($user)
    {
        Log::info('2FA required for user', ['user_id' => $user->id]);
        return redirect()->route('2fa.show');
    }

    protected function redirectBasedOnRole($user)
    {
        $redirectPath = $this->determineRedirectPath($user);
        
        Log::info('Successful login redirect', [
            'user_id' => $user->id,
            'roles' => $user->getRoleNames(),
            'redirect_path' => $redirectPath
        ]);
        
        return redirect()->intended($redirectPath);
    }

    protected function determineRedirectPath($user)
    {
        if ($user->hasRole('Admin') || $user->is_admin) {
            return '/admin/dashboard';
        }
        
        if ($user->hasRole('Trainer')) {
            return '/trainer/dashboard';
        }
        
        return '/dashboard';
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15',
            'role' => 'required|in:Member,Trainer',
            'terms' => 'required|accepted',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verified_at' => null,
            'is_admin' => $request->role === 'Member' ? 0 : 2,
            'is_active' => true,
        ]);

        $user->assignRole($request->role);

        if ($request->role === 'Member') {
            $this->createMemberProfile($user, $request);
        } elseif ($request->role === 'Trainer') {
            $this->createTrainerProfile($user, $request);
        }

        event(new Registered($user));

        $activity_data = [
            'subject' => $user,
            'event' => config('constants.ACTIVITY_LOG.CREATED_EVENT_NAME'),
            'description' => sprintf('New user (%s) registered as %s.', $user->email, $request->role),
        ];
        try {
            saveActivityLog($activity_data);
        } catch (\Exception $e) {
            Log::error('Failed to save activity log: ' . $e->getMessage());
        }

        return redirect()->route('verification.notice')
            ->with('success', 'Registration successful! Please check your email to verify your account.');
    }

    public function logout(Request $request)
    {
        $user = auth()->user();
        $activity_data = [
            'subject' => $user,
            'event' => 'logout',
            'description' => sprintf('User (%s) logged out.', $user ? $user->email : 'unknown'),
        ];
        try {
            saveActivityLog($activity_data);
        } catch (\Exception $e) {
            Log::error('Failed to save activity log: ' . $e->getMessage());
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetPasswordForm(Request $request, $token = null)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new \Illuminate\Auth\Events\PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function verificationNotice()
    {
        Log::info('Verification notice shown for user: ' . (auth()->check() ? auth()->user()->email : 'unauthenticated'));
        return view('auth.verify-email');
    }

    public function verifyEmail(Request $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            Log::info('Email already verified, redirecting to dashboard', ['email' => $user->email]);
            return redirect()->intended('/dashboard');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            Log::info('Email verified successfully', ['email' => $user->email]);
        }

        return redirect()->intended('/dashboard')->with('verified', true);
    }

    public function resendVerificationEmail(Request $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            Log::info('Email already verified, redirecting to dashboard', ['email' => $user->email]);
            return redirect()->intended('/dashboard');
        }

        $user->sendEmailVerificationNotification();
        Log::info('Verification email resent', ['email' => $user->email]);

        return back()->with('status', 'verification-link-sent');
    }

    private function createMemberProfile(User $user, Request $request)
    {
        $defaultMembershipType = \App\Models\MembershipType::where('type_name', 'Basic Monthly')->first();

        Member::create([
            'first_name' => explode(' ', $user->name)[0],
            'last_name' => explode(' ', $user->name)[1] ?? '',
            'email' => $user->email,
            'phone' => $request->phone,
            'join_date' => now(),
            'membership_type_id' => $defaultMembershipType?->id,
            'status' => 'Active',
        ]);
    }

    private function createTrainerProfile(User $user, Request $request)
    {
        Trainer::create([
            'first_name' => explode(' ', $user->name)[0],
            'last_name' => explode(' ', $user->name)[1] ?? '',
            'email' => $user->email,
            'phone' => $request->phone,
            'hire_date' => now(),
            'is_active' => false,
        ]);
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return Str::lower($request->input('email')).'|'.$request->ip();
    }
}