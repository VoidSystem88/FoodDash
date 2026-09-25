<?php

namespace App\Http\Controllers;

use App\Models\Rider;
use App\Models\Restaurant;
use App\Models\User;
use App\Notifications\OtpVerificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    // ========================================
    // LOGIN
    // ========================================
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $creds = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($creds)) {
            return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
        }

        $user = Auth::user();

        // 1. Email verified?
        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $otp = $user->generateOtp();
            $user->notify(new OtpVerificationNotification($otp));

            session(['otp_user_id' => $user->id]);

            return redirect()->route('otp.show')
                ->with('success', 'Please verify your email first. A new code has been sent.');
        }

        // 2. Status check
        if ($user->status !== 'approved') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'email' => 'Your account is ' . $user->status . '. Please wait for admin approval.'
            ])->withInput();
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    // ========================================
    // REGISTER
    // ========================================
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:customer,restaurant,rider',
            'phone' => 'nullable|string',
            'restaurant_name' => 'required_if:role,restaurant|string|max:255',
            'restaurant_address' => 'required_if:role,restaurant|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $user = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => $data['role'],
                'status' => $data['role'] === 'customer' ? 'approved' : 'pending',
                'phone' => $data['phone'] ?? null,
            ]);

            if ($data['role'] === 'restaurant') {
                Restaurant::create([
                    'user_id' => $user->id,
                    'name' => $data['restaurant_name'],
                    'address' => $data['restaurant_address'],
                    'latitude' => $data['latitude'] ?? 8.4822,
                    'longitude' => $data['longitude'] ?? 124.6472,
                ]);
            }

            if ($data['role'] === 'rider') {
                Rider::create([
                    'user_id' => $user->id,
                    'is_online' => false,
                    'is_available' => true,
                ]);
            }

            return $user;
        });

        $otp = $user->generateOtp();
        $user->notify(new OtpVerificationNotification($otp));

        session(['otp_user_id' => $user->id]);

        return redirect()->route('otp.show')
            ->with('success', 'Registration successful! Check your email for the 6-digit verification code.');
    }

    // ========================================
    // OTP VERIFICATION
    // ========================================
    public function showOtpForm()
    {
        if (!session('otp_user_id')) {
            return redirect()->route('login')
                ->withErrors(['email' => 'No pending verification. Please register or login again.']);
        }

        return view('auth.verify-otp', [
            'email' => User::find(session('otp_user_id'))?->email,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $userId = session('otp_user_id');

        if (!$userId) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Session expired. Please login again.']);
        }

        $user = User::find($userId);

        if (!$user) {
            session()->forget('otp_user_id');
            return redirect()->route('login')
                ->withErrors(['email' => 'User not found. Please register again.']);
        }

        $key = 'otp-verify:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'otp' => "Too many attempts. Please try again in {$seconds} seconds."
            ]);
        }

        if (!$user->verifyOtp($request->otp)) {
            RateLimiter::hit($key, 600);

            if (!$user->hasValidOtp()) {
                return back()->withErrors([
                    'otp' => 'Code expired. Click "Resend code" to get a new one.'
                ]);
            }

            if ($user->otp_attempts >= 5) {
                return back()->withErrors([
                    'otp' => 'Too many failed attempts. Click "Resend code" for a new one.'
                ]);
            }

            return back()->withErrors([
                'otp' => 'Invalid code. Please try again. (' . (5 - $user->otp_attempts) . ' attempts left)'
            ]);
        }

        RateLimiter::clear($key);
        session()->forget('otp_user_id');

        if ($user->isCustomer()) {
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('dashboard')
                ->with('success', 'Email verified! Welcome to FoodDash.');
        }

        return redirect()->route('login')
            ->with('success', 'Email verified! Please wait for admin approval before logging in.');
    }

    public function resendOtp(Request $request)
    {
        $userId = session('otp_user_id');

        if (!$userId) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Session expired. Please login again.']);
        }

        $user = User::find($userId);

        if (!$user) {
            session()->forget('otp_user_id');
            return redirect()->route('login')
                ->withErrors(['email' => 'User not found.']);
        }

        $key = 'otp-resend:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'otp' => "Please wait {$seconds} seconds before requesting a new code."
            ]);
        }

        RateLimiter::hit($key, 60);

        $otp = $user->generateOtp();
        $user->notify(new OtpVerificationNotification($otp));

        return back()->with('success', 'A new verification code has been sent to your email.');
    }

    // ========================================
    // LOGOUT
    // ========================================
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}