<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\TwoFactorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

// Guest routes (not authenticated)
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [App\Http\Controllers\Auth\AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\AuthController::class, 'login']);
    
    // Registration
    Route::get('/register', [App\Http\Controllers\Auth\AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\AuthController::class, 'register']);
    
    // Password Reset
    Route::get('/forgot-password', [App\Http\Controllers\Auth\AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [App\Http\Controllers\Auth\AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [App\Http\Controllers\Auth\AuthController::class, 'resetPassword'])->name('password.update');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');
    
    // Email Verification
    Route::get('/email/verify', [App\Http\Controllers\Auth\AuthController::class, 'verificationNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\AuthController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [App\Http\Controllers\Auth\AuthController::class, 'resendVerificationEmail'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    
    // Two-Factor Authentication
    Route::get('/2fa', [App\Http\Controllers\Auth\TwoFactorController::class, 'show'])->name('2fa.show');
    Route::post('/2fa/enable', [App\Http\Controllers\Auth\TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::post('/2fa/disable', [App\Http\Controllers\Auth\TwoFactorController::class, 'disable'])->name('2fa.disable');
    Route::get('/2fa/challenge', [App\Http\Controllers\Auth\TwoFactorController::class, 'challenge'])->name('2fa.challenge');
    Route::post('/2fa/verify', [App\Http\Controllers\Auth\TwoFactorController::class, 'verify'])->name('2fa.verify');
});
