<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class TwoFactorController extends Controller
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    public function show()
    {
        $user = Auth::user();
        
        if (!$user->two_factor_secret) {
            $user->two_factor_secret = $this->google2fa->generateSecretKey();
            $user->save();
        }

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $user->two_factor_secret
        );

        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new ImagickImageBackEnd()
        );
        
        $writer = new Writer($renderer);
        $qrCode = base64_encode($writer->writeString($qrCodeUrl));

        return view('auth.two-factor', compact('qrCode'));
    }

    public function enable(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();
        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->code);

        if ($valid) {
            $user->two_factor_enabled = true;
            $user->save();

            return redirect()->back()->with('success', 'Two-factor authentication enabled successfully!');
        }

        return redirect()->back()->withErrors(['code' => 'Invalid verification code.']);
    }

    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password'
        ]);

        $user = Auth::user();
        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->save();

        return redirect()->back()->with('success', 'Two-factor authentication disabled successfully!');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6'
        ]);

        $user = Auth::user();
        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $request->code);

        if ($valid) {
            session(['2fa_verified' => true]);
            return redirect()->intended('/dashboard');
        }

        return redirect()->back()->withErrors(['code' => 'Invalid verification code.']);
    }

    public function challenge()
    {
        return view('auth.two-factor-challenge');
    }
}
