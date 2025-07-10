<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Member;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        \Log::debug('Showing login form', ['path' => request()->path()]);
        return view('auth.login');
    }

    public function login(Request $request)
    {
        \Log::info('Login form submitted with: ', $request->all());

        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            \Log::info('Auth attempt successful for: ' . $request->email);
            $request->session()->regenerate();
            \Log::info('Session regenerated for user: ' . auth()->user()->email);
            $request->session()->put('test_key', 'test_value');
            \Log::info('Session test key: ' . $request->session()->get('test_key'));

            $user = auth()->user();
            $activity_data = [
                'subject' => $user,
                'event' => 'login',
                'description' => sprintf('User (%s) logged in successfully.', $user->email),
            ];
            try {
                saveActivityLog($activity_data);
            } catch (\Exception $e) {
                \Log::error('Failed to save activity log: ' . $e->getMessage());
            }

            \Log::info('User roles: ' . $user->getRoleNames()->toJson());
            \Log::debug('Checking user active status', ['email' => $user->email, 'is_active' => $user->is_active ?? 'not set']);
            if (!($user->is_active ?? true)) {
                Auth::logout();
                $request->session()->invalidate();
                \Log::warning('Login failed: User is inactive', ['email' => $user->email]);
                return redirect()->route('login')->with('error', 'Your account is inactive. Please contact support.');
            }

            if (!$user->hasVerifiedEmail()) {
                \Log::info('User email not verified, redirecting to verification notice', ['email' => $user->email]);
                return redirect()->route('verification.notice');
            }

            if ($this->requiresTwoFactorAuth($user)) {
                \Log::info('User requires 2FA, redirecting to 2FA challenge', ['email' => $user->email]);
                return redirect()->route('2fa.show');
            }

            return $this->redirectBasedOnRole();
        }

        \Log::info('Failed login attempt', [
            'email' => $request->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password'));
    }

    protected function requiresTwoFactorAuth($user)
    {
        return $user->two_factor_secret !== null;
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
            'is_active' => true, // Ensure new users are active
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
            \Log::error('Failed to save activity log: ' . $e->getMessage());
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
            \Log::error('Failed to save activity log: ' . $e->getMessage());
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
        \Log::info('Verification notice shown for user: ' . (auth()->check() ? auth()->user()->email : 'unauthenticated'));
        return view('auth.verify-email');
    }

    public function verifyEmail(Request $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            \Log::info('Email already verified, redirecting to dashboard', ['email' => $user->email]);
            return redirect()->intended('/dashboard');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            \Log::info('Email verified successfully', ['email' => $user->email]);
        }

        return redirect()->intended('/dashboard')->with('verified', true);
    }

    public function resendVerificationEmail(Request $request)
    {
        $user = $request->user();
        if ($user->hasVerifiedEmail()) {
            \Log::info('Email already verified, redirecting to dashboard', ['email' => $user->email]);
            return redirect()->intended('/dashboard');
        }

        $user->sendEmailVerificationNotification();
        \Log::info('Verification email resent', ['email' => $user->email]);

        return back()->with('status', 'verification-link-sent');
    }

    private function redirectBasedOnRole()
    {
        $user = auth()->user();
        \Log::info('Redirecting user to dashboard', [
            'email' => $user->email,
            'roles' => $user->getRoleNames()->toJson(),
            'session_id' => request()->session()->getId(),
        ]);
        return redirect()->intended('/dashboard');
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
}