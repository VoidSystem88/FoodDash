<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Rider;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminProfileController extends Controller
{
    /**
     * Admin profile page — dedicated view.
     */
    public function index()
    {
        $user = auth()->user();

        // Update last seen
        DB::table('users')->where('id', $user->id)->update(['last_seen_at' => now()]);
        $user = $user->fresh();

        // Admin-specific system stats
        $stats = [
            'total_users' => User::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_restaurants' => User::where('role', 'restaurant')->count(),
            'total_riders' => User::where('role', 'rider')->count(),
            'total_orders' => Order::count(),
            'delivered_orders' => Order::where('status', 'delivered')->count(),
            'total_revenue' => Order::where('status', 'delivered')->sum('total_amount'),
            'total_commission' => Order::where('status', 'delivered')->sum('commission_amount'),
        ];

        // Recent admin activity
        $recentActivity = [
            'pending_accounts' => User::where('status', 'pending')->count(),
            'flagged_reviews' => \App\Models\Review::where('status', 'flagged')->count(),
            'active_restaurants' => Restaurant::where('is_open', true)->count(),
            'online_riders' => Rider::where('is_online', true)->count(),
        ];

        return view('admin.profile.index', compact('user', 'stats', 'recentActivity'));
    }

    /**
     * Admin avatar editor page.
     */
    public function avatar()
    {
        return view('admin.profile.avatar', ['user' => auth()->user()]);
    }

    /**
     * Upload avatar.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        $user = auth()->user();

        // Delete old
        if ($user->avatar) {
            $old = storage_path('app/public/' . $user->avatar);
            if (file_exists($old)) @unlink($old);
        }

        // Ensure directory
        $dir = storage_path('app/public/avatars');
        if (!file_exists($dir)) mkdir($dir, 0755, true);

        // Save new
        $file = $request->file('avatar');
        $filename = 'avatars/' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, basename($filename));

        $user->avatar = $filename;
        $user->save();

        return redirect()->route('admin.profile.index')
            ->with('success', 'Profile picture updated successfully!');
    }

    /**
     * Remove avatar.
     */
    public function removeAvatar(Request $request)
    {
        $user = auth()->user();

        if ($user->avatar) {
            $path = storage_path('app/public/' . $user->avatar);
            if (file_exists($path)) @unlink($path);
        }

        $user->avatar = null;
        $user->save();

        return back()->with('success', 'Profile picture removed.');
    }

    /**
     * Update admin profile (name, phone, gender).
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
        ]);

        $user->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update admin password.
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
}