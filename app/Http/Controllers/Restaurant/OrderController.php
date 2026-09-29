<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use App\Jobs\FindRiderForOrder;
use App\Models\Order;
use App\Models\SystemConfig;
use App\Notifications\OrderStatusNotification;
use App\Events\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\OrderReadyForRider;
use App\Events\OrderVerifiedForRider;
use App\Notifications\OrderReadyForRiderNotification;
use App\Notifications\OrderVerifiedForRiderNotification;
class OrderController extends Controller
{
    // ============================================
    // DASHBOARD
    // ============================================
    public function dashboard()
    {
        $restaurant = auth()->user()->restaurant;

        // ⭐ Active statuses — kasama na ang 'ready_for_pickup'
        $activeStatuses = [
            'received',
            'confirmed',
            'preparing',
            'ready_for_pickup',   // ⭐ BAGO
            'finding_rider',
            'rider_assigned',
            'picked_up',
            'out_for_delivery',
        ];

        $orders = Order::with(['customer', 'rider.user', 'items'])
            ->where('restaurant_id', $restaurant->id)
            ->whereIn('status', $activeStatuses)
            ->orderByRaw("FIELD(status, 
                'received', 
                'confirmed', 
                'preparing', 
                'ready_for_pickup', 
                'finding_rider', 
                'rider_assigned', 
                'picked_up', 
                'out_for_delivery'
            )")
            ->latest()
            ->get();

        $avgRating = Order::where('restaurant_id', $restaurant->id)
            ->whereNotNull('restaurant_rating')
            ->avg('restaurant_rating');

        $totalRatings = Order::where('restaurant_id', $restaurant->id)
            ->whereNotNull('restaurant_rating')
            ->count();

        // Pending count — para sa hero stats
        $pendingCount = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'received')
            ->count();

        // Today's stats
        $todayOrders = Order::where('restaurant_id', $restaurant->id)
            ->whereDate('created_at', today())
            ->count();

        $todaySales = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'delivered')
            ->whereDate('created_at', today())
            ->sum('food_cost');

        $ratingStats = [
            'avg' => $avgRating ? round($avgRating, 1) : null,
            'total' => $totalRatings,
            'pending' => $pendingCount,
            'today_orders' => $todayOrders,
            'today_sales' => $todaySales,
        ];

        return view('restaurant.dashboard', compact('restaurant', 'orders', 'ratingStats'));
    }

    // ============================================
    // CONFIRM ORDER — 'received' → 'confirmed'
    // ============================================
// ============================================
// CONFIRM ORDER — 'received' → 'confirmed'
// ⭐ IMMEDIATELY dispatch rider search
// ============================================
public function confirm(Request $request, Order $order)
{
    $restaurant = auth()->user()->restaurant;
    abort_unless($order->restaurant_id === $restaurant->id, 403);
    abort_unless($order->status === 'received', 422, 'Order already processed');

    $order->update(['status' => 'confirmed']);

    // Notify customer
    $order->customer->notify(new OrderStatusNotification($order->fresh()));

    // ⭐ CRITICAL: Dispatch rider search IMMEDIATELY
    \App\Jobs\FindRiderForOrder::dispatch($order->fresh());

    return back()->with('success', 'Order confirmed. Searching for rider...');
}

// ============================================
// START PREPARING — 'confirmed' → 'preparing'
// ⭐ This is the VERIFICATION trigger for the rider
// ============================================
public function ready(Request $request, Order $order)
{
    $restaurant = auth()->user()->restaurant;
    abort_unless($order->restaurant_id === $restaurant->id, 403);

    // ⭐ Accept both 'confirmed' AND 'rider_assigned'
    // Dahil sa bagong flow, ang rider ay pwedeng mag-accept bago mag-start preparing ang restaurant
    $allowedStatuses = ['confirmed', 'rider_assigned'];

    if (!in_array($order->status, $allowedStatuses)) {
        return back()->with('error',
            'Order must be "confirmed" or "rider_assigned" first. Current: ' . $order->status
        );
    }

    $order->update([
        'status' => 'preparing',
        'restaurant_started_preparing_at' => now(),
    ]);

    // Notify customer
    $order->customer->notify(new OrderStatusNotification($order->fresh()));

    // ⭐ Notify rider (kung may naka-assign na) na verified na
    if ($order->rider && $order->rider->user) {
    $order->rider->user->notify(
        new \App\Notifications\OrderReadyForRiderNotification($order->fresh())
    );

    // ⭐ Broadcast sa order channel (existing)
    try {
        broadcast(new \App\Events\OrderReadyForRider($order->fresh()));
    } catch (\Throwable $e) {
        \Log::warning('OrderReadyForRider broadcast failed: ' . $e->getMessage());
    }

    // ⭐ BAGO: Broadcast din sa rider channel
    try {
        broadcast(new \App\Events\OrderReadyForRiderBroadcast($order->fresh()));
        \Log::info("✅ OrderReadyForRiderBroadcast sent for order #{$order->id} to rider #{$order->rider_id}");
    } catch (\Throwable $e) {
        \Log::error("❌ OrderReadyForRiderBroadcast failed: " . $e->getMessage());
    }
} else {
        // Kung wala pang rider, i-dispatch ulit ang search
        \App\Jobs\FindRiderForOrder::dispatch($order->fresh());
    }

    try {
        broadcast(new OrderStatusUpdated($order->fresh()));
    } catch (\Throwable $e) {
        \Log::warning('OrderStatusUpdated broadcast failed: ' . $e->getMessage());
    }

    return back()->with('success', 'Order marked as preparing. Rider notified!');
}

// ============================================
// ⭐ MARK AS READY — 'preparing' → 'ready_for_pickup'
// Notify rider na ready na ang food
// ============================================
public function markReady(Order $order)
{
    $restaurant = auth()->user()->restaurant;
    abort_unless($order->restaurant_id === $restaurant->id, 403);

    // ⭐ Accept both 'preparing' AND 'rider_assigned'
    $allowedStatuses = ['preparing', 'rider_assigned'];

    if (!in_array($order->status, $allowedStatuses)) {
        return back()->with('error',
            'Order must be "preparing" first. Current: ' . $order->status
        );
    }

    // ⭐ ALWAYS set to 'ready_for_pickup' (hindi 'rider_assigned')
    $order->update([
        'status' => 'ready_for_pickup',
        'restaurant_marked_ready_at' => now(),
    ]);

    // Notify customer
    $order->customer->notify(new OrderStatusNotification($order->fresh()));

    // ⭐ Notify rider (kung may naka-assign na) na ready na ang food
    if ($order->rider && $order->rider->user) {
        $order->rider->user->notify(
            new \App\Notifications\OrderReadyForRiderNotification($order->fresh())
        );

        try {
            broadcast(new \App\Events\OrderReadyForRider($order->fresh()));
        } catch (\Throwable $e) {
            \Log::warning('OrderReadyForRider broadcast failed: ' . $e->getMessage());
        }
    } else {
        // Kung wala pang rider, i-dispatch ulit ang search
        \App\Jobs\FindRiderForOrder::dispatch($order->fresh());
    }

    try {
        broadcast(new OrderStatusUpdated($order->fresh()));
    } catch (\Throwable $e) {
        \Log::warning('OrderStatusUpdated broadcast failed: ' . $e->getMessage());
    }

    return back()->with('success', 'Order ready! Rider notified.');
}

    // ============================================
    // REJECT ORDER
    // ============================================
    public function reject(Request $request, Order $order)
    {
        $restaurant = auth()->user()->restaurant;
        abort_unless($order->restaurant_id === $restaurant->id, 403);

        $data = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $order->update([
            'status' => 'rejected',
            'rejection_reason' => $data['rejection_reason'],
        ]);

        // Notify customer
        $order->customer->notify(new OrderStatusNotification($order->fresh()));

        return back()->with('success', 'Order rejected.');
    }

    // ============================================
    // ⭐ START PREPARING — 'confirmed' → 'preparing'
    // ============================================
    


    // ============================================
    // EXTERNAL ORDER
    // ============================================
    public function storeExternal(Request $request)
    {
        $restaurant = auth()->user()->restaurant;

        $data = $request->validate([
            'customer_name' => 'required|string',
            'delivery_address' => 'required|string',
            'delivery_lat' => 'required|numeric',
            'delivery_lng' => 'required|numeric',
            'food_cost' => 'required|numeric|min:0',
        ]);

        $config = SystemConfig::current();
        $deliveryFee = $config->default_delivery_fee;
        $commissionRate = $config->commission_rate;
        $commissionAmount = round($data['food_cost'] * ($commissionRate / 100), 2);
        $restaurantEarnings = $data['food_cost'] - $commissionAmount;

        $order = Order::create([
            'customer_id' => auth()->id(),
            'restaurant_id' => $restaurant->id,
            'status' => 'confirmed',
            'food_cost' => $data['food_cost'],
            'delivery_fee' => $deliveryFee,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'restaurant_earnings' => $restaurantEarnings,
            'total_amount' => $data['food_cost'] + $deliveryFee,
            'delivery_address' => $data['delivery_address'],
            'delivery_lat' => $data['delivery_lat'],
            'delivery_lng' => $data['delivery_lng'],
            'is_external_order' => true,
        ]);

        FindRiderForOrder::dispatch($order);

        return back()->with('success', 'External order added. Searching for rider...');
    }

    // ============================================
    // TOGGLE OPEN/CLOSE
    // ============================================
    public function toggleOpen(Request $request)
    {
        $restaurant = auth()->user()->restaurant;
        $restaurant->update(['is_open' => !$restaurant->is_open]);

        return back()->with('success', $restaurant->is_open
            ? 'Restaurant is now OPEN.'
            : 'Restaurant is now CLOSED.');
    }

    // ============================================
    // PROFILE
    // ============================================
    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'prep_time_minutes' => 'nullable|integer|min:1|max:120',
            'badge' => 'nullable|string|max:50',
        ]);

        $restaurant = auth()->user()->restaurant;
        $restaurant->update($data);

        return back()->with('success', 'Restaurant profile updated.');
    }

    // ============================================
    // IMAGE UPLOADS
    // ============================================
    public function uploadCover(Request $request)
    {
        $request->validate([
            'cover' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        $restaurant = auth()->user()->restaurant;

        if ($restaurant->cover_image) {
            $old = storage_path('app/public/' . $restaurant->cover_image);
            if (file_exists($old)) @unlink($old);
        }

        $dir = storage_path('app/public/restaurants');
        if (!file_exists($dir)) mkdir($dir, 0755, true);

        $file = $request->file('cover');
        $filename = 'restaurants/cover_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, basename($filename));

        $restaurant->cover_image = $filename;
        $restaurant->save();

        return back()->with('success', 'Cover image updated!');
    }

    public function uploadProfileImage(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        $restaurant = auth()->user()->restaurant;

        if ($restaurant->profile_image) {
            $old = storage_path('app/public/' . $restaurant->profile_image);
            if (file_exists($old)) @unlink($old);
        }

        $dir = storage_path('app/public/restaurants');
        if (!file_exists($dir)) mkdir($dir, 0755, true);

        $file = $request->file('profile_image');
        $filename = 'restaurants/profile_' . uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();
        $file->move($dir, basename($filename));

        $restaurant->profile_image = $filename;
        $restaurant->save();

        return back()->with('success', 'Profile image updated!');
    }

    public function removeCover()
    {
        $restaurant = auth()->user()->restaurant;

        if ($restaurant->cover_image) {
            $path = storage_path('app/public/' . $restaurant->cover_image);
            if (file_exists($path)) @unlink($path);
        }

        $restaurant->cover_image = null;
        $restaurant->save();

        return back()->with('success', 'Cover image removed.');
    }

    public function removeProfileImage()
    {
        $restaurant = auth()->user()->restaurant;

        if ($restaurant->profile_image) {
            $path = storage_path('app/public/' . $restaurant->profile_image);
            if (file_exists($path)) @unlink($path);
        }

        $restaurant->profile_image = null;
        $restaurant->save();

        return back()->with('success', 'Profile image removed.');
    }

    // ============================================
    // OPERATING HOURS
    // ============================================
    public function hours()
    {
        $restaurant = auth()->user()->restaurant;
        $existing = $restaurant->hours->keyBy('day_of_week');
        $days = [];
        $dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        for ($i = 0; $i < 7; $i++) {
            $days[$i] = [
                'day_of_week' => $i,
                'day_name' => $dayNames[$i],
                'is_open' => $existing->has($i) ? $existing[$i]->is_open : false,
                'open_time' => $existing->has($i) ? $existing[$i]->open_time : '09:00',
                'close_time' => $existing->has($i) ? $existing[$i]->close_time : '21:00',
            ];
        }

        return view('restaurant.hours', compact('restaurant', 'days'));
    }

    public function updateHours(Request $request)
    {
        $restaurant = auth()->user()->restaurant;

        $data = $request->validate([
            'days' => 'required|array|size:7',
            'days.*.day_of_week' => 'required|integer|between:0,6',
            'days.*.is_open' => 'nullable|boolean',
            'days.*.open_time' => 'nullable|date_format:H:i',
            'days.*.close_time' => 'nullable|date_format:H:i|after:days.*.open_time',
        ]);

        foreach ($data['days'] as $day) {
            $isOpen = !empty($day['is_open']);

            \App\Models\RestaurantHour::updateOrCreate(
                [
                    'restaurant_id' => $restaurant->id,
                    'day_of_week' => $day['day_of_week'],
                ],
                [
                    'is_open' => $isOpen,
                    'open_time' => $isOpen ? ($day['open_time'] ?? '09:00') : null,
                    'close_time' => $isOpen ? ($day['close_time'] ?? '21:00') : null,
                ]
            );
        }

        \Artisan::call('restaurants:auto-toggle');

        return back()->with('success', 'Operating hours updated successfully.');
    }

    // ============================================
    // ORDERS LIST
    // ============================================
    public function orders(Request $request)
    {
        $restaurant = auth()->user()->restaurant;

        $query = Order::with(['customer', 'rider.user', 'items'])  // ⭐ BAGO: rider.user
            ->where('restaurant_id', $restaurant->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        $totalSales = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'delivered')
            ->sum('food_cost');

        $totalOrders = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'delivered')
            ->count();

        $avgRating = Order::where('restaurant_id', $restaurant->id)
            ->whereNotNull('restaurant_rating')
            ->avg('restaurant_rating');

        $stats = [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'avg_rating' => $avgRating ? round($avgRating, 2) : 'N/A',
        ];

        return view('restaurant.orders', compact('orders', 'stats'));
    }

    // ============================================
    // ANALYTICS
    // ============================================
    public function analytics(Request $request)
    {
        $restaurant = auth()->user()->restaurant;

        $query = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'delivered');

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $totalSales = (clone $query)->sum('food_cost');
        $totalOrders = (clone $query)->count();
        $avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        $avgRating = Order::where('restaurant_id', $restaurant->id)
            ->whereNotNull('restaurant_rating')
            ->avg('restaurant_rating');

        $stats = [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'avg_order_value' => $avgOrderValue,
            'avg_rating' => $avgRating ? round($avgRating, 2) : 'N/A',
        ];

        $topItems = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.restaurant_id', $restaurant->id)
            ->where('orders.status', 'delivered')
            ->select(
                'order_items.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.price * order_items.quantity) as total_revenue')
            )
            ->groupBy('order_items.name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        $dailySales = Order::where('restaurant_id', $restaurant->id)
            ->where('status', 'delivered')
            ->where('created_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(food_cost) as total')
            )
            ->groupBy('date')
            ->orderByDesc('date')
            ->get();

        return view('restaurant.analytics', compact('stats', 'topItems', 'dailySales'));
    }
}