<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    /**
     * Advanced settings page.
     */
    public function index()
    {
        return view('settings.index', ['user' => auth()->user()]);
    }

    /**
     * Update personal info.
     */
    public function updateProfile(Request $request)
{
    $user = auth()->user();

    $data = $request->validate([
        'name' => 'required|string|max:255',
        'phone' => 'nullable|string|max:20',
        'gender' => 'nullable|in:male,female',
    ]);

    $user->name = $data['name'];
    $user->phone = $data['phone'] ?? null;

    // Gender — para sa customer marker icon lang
    if ($user->isCustomer()) {
        $user->gender = $data['gender'] ?? null;
    }

    $user->save();

    return back()->with('success', 'Profile updated successfully.');
}

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Delete account (customer only).
     */
    public function destroy(Request $request)
    {
        $user = auth()->user();

        if (!$user->isCustomer()) {
            return back()->withErrors(['password' => 'Account deletion is only available for customers.']);
        }

        $request->validate([
            'password' => 'required|string',
            'confirm_delete' => 'required|accepted',
        ]);

        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Incorrect password. Account not deleted.']);
        }

        $hasActiveOrders = Order::where('customer_id', $user->id)
            ->whereNotIn('status', ['delivered', 'cancelled', 'rejected', 'no_rider'])
            ->exists();

        if ($hasActiveOrders) {
            return back()->withErrors(['password' => 'Cannot delete account — may active order(s) ka pa.']);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        DB::transaction(function () use ($user) {
            if ($user->avatar) {
                $path = storage_path('app/public/' . $user->avatar);
                if (file_exists($path)) @unlink($path);
            }
            $user->delete();
        });

        return redirect()->route('login')
            ->with('success', 'Your account has been permanently deleted. 😢');
    }
}