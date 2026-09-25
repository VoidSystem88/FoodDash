<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    /**
     * Profile display page — read-only view.
     */
    public function index()
    {
        $user = auth()->user();

        // Update last_seen_at
        DB::table('users')->where('id', $user->id)->update(['last_seen_at' => now()]);
        $user = $user->fresh();

        // Stats
        $stats = $this->getStats($user);

        return view('profile.index', compact('user', 'stats'));
    }

    /**
     * Avatar editor page.
     */
    public function avatar()
    {
        return view('profile.avatar', ['user' => auth()->user()]);
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

        return response()->json([
            'ok' => true,
            'avatar_url' => asset('storage/' . $filename),
            'message' => 'Profile picture updated!',
        ]);
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
     * Get stats per role.
     */
    protected function getStats($user): ?array
    {
        if ($user->isCustomer()) {
            return [
                'total_orders' => Order::where('customer_id', $user->id)->count(),
                'delivered_orders' => Order::where('customer_id', $user->id)->where('status', 'delivered')->count(),
                'total_spent' => Order::where('customer_id', $user->id)->where('status', 'delivered')->sum('total_amount'),
            ];
        }

        if ($user->isRider() && $user->rider) {
            return [
                'total_deliveries' => Order::where('rider_id', $user->rider->id)->where('status', 'delivered')->count(),
                'total_earnings' => Order::where('rider_id', $user->rider->id)->where('status', 'delivered')->sum('delivery_fee'),
                'avg_rating' => Order::where('rider_id', $user->rider->id)->whereNotNull('rider_rating')->avg('rider_rating'),
            ];
        }

        if ($user->isRestaurant() && $user->restaurant) {
            return [
                'total_orders' => Order::where('restaurant_id', $user->restaurant->id)->where('status', 'delivered')->count(),
                'total_sales' => Order::where('restaurant_id', $user->restaurant->id)->where('status', 'delivered')->sum('food_cost'),
                'avg_rating' => Order::where('restaurant_id', $user->restaurant->id)->whereNotNull('restaurant_rating')->avg('restaurant_rating'),
            ];
        }

        return null;
    }
}