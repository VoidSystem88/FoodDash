<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\PasswordResetOtpNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class PasswordResetController extends Controller
{
    // ========================================
    // STEP 1: Request OTP
    // ========================================
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        // Always show success message para hindi ma-enumerate ang emails
        if (!$user) {
            session(['reset_email' => $request->email]);

            return redirect()->route('password.verify.show')
                ->with('success', 'If your email exists in our system, a 6-digit code has been sent.');
        }

        // Throttle: 1 OTP request per 60 seconds
        $key = 'password-reset-otp:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Please wait {$seconds} seconds before requesting a new code."
            ]);
        }

        RateLimiter::hit($key, 60);

        // Generate OTP
        $otp = $user->generatePasswordResetOtp();
        $user->notify(new PasswordResetOtpNotification($otp));

        // ✅ TAMANG PARAAN: I-save sa session (persistent)
        session(['reset_email' => $user->email]);

        return redirect()->route('password.verify.show')
            ->with('success', 'If your email exists in our system, a 6-digit code has been sent.');
    }

    // ========================================
    // STEP 2: Show OTP verification page
    // ========================================
    public function showVerifyOtp()
    {
        $email = session('reset_email');

        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Please enter your email first.']);
        }

        return view('auth.forgot-password-verify', compact('email'));
    }

    // ========================================
    // STEP 3: Verify OTP
    // ========================================
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $email = session('reset_email');

        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Session expired. Please try again.']);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'User not found.']);
        }

        // Rate limit: 5 attempts per 10 minutes
        $key = 'password-reset-verify:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'otp' => "Too many attempts. Please try again in {$seconds} seconds."
            ]);
        }

        if (!$user->verifyPasswordResetOtp($request->otp)) {
            RateLimiter::hit($key, 600);

            if (!$user->hasValidPasswordResetOtp()) {
                return back()->withErrors([
                    'otp' => 'Code expired. Click "Resend code" to get a new one.'
                ]);
            }

            if ($user->password_reset_otp_attempts >= 5) {
                return back()->withErrors([
                    'otp' => 'Too many failed attempts. Click "Resend code" for a new one.'
                ]);
            }

            return back()->withErrors([
                'otp' => 'Invalid code. Please try again. (' . (5 - $user->password_reset_otp_attempts) . ' attempts left)'
            ]);
        }

        RateLimiter::clear($key);

        // Mark as verified — store sa session
        session(['password_reset_verified_user_id' => $user->id]);

        return redirect()->route('password.reset.form')
            ->with('success', 'Code verified! Enter your new password.');
    }

    // ========================================
    // STEP 4: Resend OTP
    // ========================================
    public function resendOtp(Request $request)
    {
        $email = session('reset_email');

        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Session expired. Please try again.']);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'User not found.']);
        }

        $key = 'password-reset-resend:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'otp' => "Please wait {$seconds} seconds before requesting a new code."
            ]);
        }

        RateLimiter::hit($key, 60);

        $otp = $user->generatePasswordResetOtp();
        $user->notify(new PasswordResetOtpNotification($otp));

        return back()->with('success', 'A new verification code has been sent to your email.');
    }

    // ========================================
    // STEP 5: Show reset password form
    // ========================================
    public function showResetForm(Request $request)
    {
        $userId = session('password_reset_verified_user_id');

        if (!$userId) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Please verify your OTP first.']);
        }

        $user = User::find($userId);

        if (!$user || !$user->hasVerifiedPasswordResetOtp()) {
            session()->forget('password_reset_verified_user_id');
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Verification expired. Please start again.']);
        }

        return view('auth.reset-password', ['email' => $user->email]);
    }

    // ========================================
    // STEP 6: Reset password
    // ========================================
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $userId = session('password_reset_verified_user_id');

        if (!$userId) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Session expired. Please start again.']);
        }

        $user = User::find($userId);

        if (!$user || !$user->hasVerifiedPasswordResetOtp()) {
            session()->forget('password_reset_verified_user_id');
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Verification expired. Please start again.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        $user->clearPasswordResetOtp();
        session()->forget(['password_reset_verified_user_id', 'reset_email']);

        return redirect()->route('login')
            ->with('success', 'Password reset successfully! You can now log in.');
    }
}