<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {

    // LOGIN
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // REGISTER
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // OTP VERIFICATION (email verification)
    Route::get('/verify-otp', [AuthController::class, 'showOtpForm'])->name('otp.show');
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/verify-otp/resend', [AuthController::class, 'resendOtp'])->name('otp.resend');

    // ========================================
    // PASSWORD RESET WITH OTP
    // ========================================
    Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])
        ->name('password.request');

    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
        ->name('password.email');

    Route::get('/forgot-password/verify', [PasswordResetController::class, 'showVerifyOtp'])
        ->name('password.verify.show');

    Route::post('/forgot-password/verify', [PasswordResetController::class, 'verifyOtp'])
        ->name('password.verify.otp');

    Route::post('/forgot-password/resend', [PasswordResetController::class, 'resendOtp'])
        ->name('password.resend.otp');

    Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])
        ->name('password.reset.form');

    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
        ->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// EMAIL VERIFICATION (Laravel built-in)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard')->with('success', 'Email verified successfully!');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});