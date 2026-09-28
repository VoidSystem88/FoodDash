<?php

namespace App\Http\Controllers\Rider;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Message;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    
    public function dashboard()
    {
        $rider = auth()->user()->rider;

        $currentOrder = Order::with(['restaurant', 'customer', 'items', 'payment'])
            ->where('rider_id', $rider->id)
            ->whereIn('status', ['rider_assigned', 'picked_up', 'out_for_delivery'])
            ->latest()
            ->first();

        $completedToday = Order::where('rider_id', $rider->id)
            ->where('status', 'delivered')
            ->whereDate('updated_at', today())
            ->count();

        $earningsToday = Order::where('rider_id', $rider->id)
            ->where('status', 'delivered')
            ->whereDate('updated_at', today())
            ->sum('delivery_fee');

        return view('rider.dashboard', compact('rider', 'currentOrder', 'completedToday', 'earningsToday'));
    }
    /**
     * Get unread chat count for active order.
     */
        /**
     * Chat inbox — listahan ng lahat ng orders na may messages.
     */
    public function chatInbox(Request $request)
{
    $rider = auth()->user()->rider;
    $userId = auth()->id();
    $showHidden = $request->boolean('show_hidden');

    if (!$rider) {
        return view('rider.chat-inbox', [
            'conversations' => collect(),
            'showHidden' => $showHidden,
        ]);
    }

    $query = Order::with(['restaurant', 'customer', 'items'])
        ->where('rider_id', $rider->id)
        ->where(function ($q) {
            // May existing messages
            $q->whereHas('messages')
              // O active order (para lumabas agad ang chat kahit walang messages)
              ->orWhere(function ($sub) {
                  $sub->whereIn('status', ['rider_assigned', 'picked_up', 'out_for_delivery']);
              });
        });

        // Filter: kung hindi show_hidden, hindi ipakita ang hidden (o may bagong message)
        if (!$showHidden) {
            $query->where(function ($q) {
                $q->whereNull('hidden_for_rider_at')
                  ->orWhereHas('messages', function ($sub) {
                      $sub->whereColumn('messages.created_at', '>', 'orders.hidden_for_rider_at');
                  });
            });
        }

        $orders = $query
            ->orderByRaw("
                CASE
                    WHEN status IN ('rider_assigned', 'picked_up', 'out_for_delivery') THEN 0
                    ELSE 1
                END
            ")
            ->orderByDesc('updated_at')
            ->get();

        $conversations = $orders->map(function ($order) use ($userId) {
            $lastMessage = $order->messages()->latest()->first();
            $unreadCount = $order->messages()
                ->where('sender_id', '!=', $userId)
                ->whereNull('read_at')
                ->count();
            $totalMessages = $order->messages()->count();

            return [
                'order' => $order,
                'last_message' => $lastMessage,
                'unread_count' => $unreadCount,
                'total_messages' => $totalMessages,
                'is_active' => in_array($order->status, [
                    'rider_assigned', 'picked_up', 'out_for_delivery'
                ]),
                'is_hidden' => !is_null($order->hidden_for_rider_at),
            ];
        });

        return view('rider.chat-inbox', compact('conversations', 'showHidden'));
    }

  

    /**
     * Unread count for active order.
     */
    public function unreadActive()
    {
        $rider = auth()->user()->rider;

        if (!$rider) {
            return response()->json(['unread' => 0]);
        }

        $activeOrder = Order::where('rider_id', $rider->id)
            ->whereIn('status', ['rider_assigned', 'picked_up', 'out_for_delivery'])
            ->first();

        if (!$activeOrder) {
            return response()->json(['unread' => 0]);
        }

        $unread = $activeOrder->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['unread' => $unread]);
    }

    /**
     * Clear all chats (soft hide — proof pa rin).
     */
    public function clearChats()
    {
        $rider = auth()->user()->rider;

        if (!$rider) {
            return back()->with('error', 'Rider not found.');
        }

        $updatedCount = Order::where('rider_id', $rider->id)
            ->whereHas('messages')
            ->update(['hidden_for_rider_at' => now()]);

        return back()->with('success', "Hidden {$updatedCount} chat conversations. Messages are still kept for records.");
    }
    
    public function history()
    {
        $rider = auth()->user()->rider;

        $orders = Order::with(['restaurant', 'customer', 'items'])
            ->where('rider_id', $rider->id)
            ->where('status', 'delivered')
            ->latest()
            ->paginate(20);

        $totalEarnings = Order::where('rider_id', $rider->id)
            ->where('status', 'delivered')
            ->sum('delivery_fee');

        $totalDeliveries = Order::where('rider_id', $rider->id)
            ->where('status', 'delivered')
            ->count();

        $avgRating = Order::where('rider_id', $rider->id)
            ->whereNotNull('rider_rating')
            ->avg('rider_rating');

        $stats = [
            'total_earnings' => $totalEarnings,
            'total_deliveries' => $totalDeliveries,
            'avg_rating' => $avgRating ? round($avgRating, 2) : 'N/A',
        ];

        return view('rider.history', compact('orders', 'stats'));
    }

    /**
     * Rider earnings dashboard.
     */
    public function earnings(Request $request)
    {
        $rider = auth()->user()->rider;

        // Period: 7 days, 30 days, or this month
        $period = $request->query('period', '7days');

        $from = match ($period) {
            '30days' => now()->subDays(30)->startOfDay(),
            'month' => now()->startOfMonth(),
            default => now()->subDays(7)->startOfDay(),
        };

        $to = now();

        // Base query
        $query = Order::where('rider_id', $rider->id)
            ->where('status', 'delivered')
            ->whereBetween('updated_at', [$from, $to]);

        // Summary stats
        $totalEarnings = (clone $query)->sum('delivery_fee');
        $totalDeliveries = (clone $query)->count();
        $avgPerDelivery = $totalDeliveries > 0 ? $totalEarnings / $totalDeliveries : 0;

        // Daily breakdown
        $dailyEarnings = (clone $query)
            ->selectRaw('DATE(updated_at) as date')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(delivery_fee) as total')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Best day
        $bestDay = $dailyEarnings->sortByDesc('total')->first();

        // Kunin lahat ng araw sa range (may zero-fill sa chart)
        $allDays = [];
        $current = $from->copy()->startOfDay();
        $endOfDay = $to->copy()->endOfDay();

        while ($current <= $endOfDay) {
            $dateStr = $current->format('Y-m-d');
            $existing = $dailyEarnings->firstWhere('date', $dateStr);

            $allDays[] = [
                'date' => $dateStr,
                'label' => $current->format('M d'),
                'count' => $existing->count ?? 0,
                'total' => (float) ($existing->total ?? 0),
            ];

            $current->addDay();
        }

        $chartLabels = collect($allDays)->pluck('label')->values();
        $chartData = collect($allDays)->pluck('total')->values();

        return view('rider.earnings', compact(
            'period',
            'from',
            'to',
            'totalEarnings',
            'totalDeliveries',
            'avgPerDelivery',
            'dailyEarnings',
            'bestDay',
            'chartLabels',
            'chartData',
            'allDays',
        ));
    }

    public function profile()
    {
        $rider = auth()->user()->rider;

        return view('rider.profile', compact('rider'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $rider = $user->rider;

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'vehicle_type' => 'nullable|string|max:50',
            'vehicle_plate' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
        ]);

        $rider->update([
            'vehicle_type' => $data['vehicle_type'] ?? null,
            'vehicle_plate' => $data['vehicle_plate'] ?? null,
        ]);

        return back()->with('success', 'Profile updated.');
    }

        public function toggleOnline(Request $request)
    {
        $rider = auth()->user()->rider;

        // ⭐ CHECK kung may active order
        if ($rider->is_online) {
            $hasActiveOrder = \App\Models\Order::where('rider_id', $rider->id)
                ->whereIn('status', ['rider_assigned', 'picked_up', 'out_for_delivery'])
                ->exists();

            if ($hasActiveOrder) {
                return response()->json([
                    'ok' => false,
                    'is_online' => true,   // Force online
                    'is_available' => $rider->is_available,
                    'message' => 'Cannot go offline — you have an active delivery. Complete it first.',
                ], 422);
            }
        }

        // ⭐ Toggle online status
        $rider->update([
            'is_online' => !$rider->is_online,
            'is_available' => !$rider->is_online,   // Toggle din availability
        ]);

        return response()->json([
            'ok' => true,
            'is_online' => $rider->is_online,
            'is_available' => $rider->is_available,
        ]);
    }

    public function updateLocation(Request $request)
    {
        $data = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $rider = auth()->user()->rider;

        $rider->update([
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'last_location_at' => now(),
        ]);

        // Broadcast sa customer kung may active order
        $activeOrder = Order::where('rider_id', $rider->id)
            ->whereIn('status', ['rider_assigned', 'picked_up', 'out_for_delivery'])
            ->first();

        if ($activeOrder) {
            broadcast(new \App\Events\RiderLocationUpdated(
                orderId: $activeOrder->id,
                latitude: (float) $data['latitude'],
                longitude: (float) $data['longitude'],
                riderName: auth()->user()->name,
            ));
        }

        return response()->json(['ok' => true]);
    }
        /**
     * Chat page para sa active order.
     */
        public function chat()
    {
        $rider = auth()->user()->rider;

        // Priority: active order
        $currentOrder = Order::with(['restaurant', 'customer', 'items', 'payment'])
            ->where('rider_id', $rider->id)
            ->whereIn('status', ['rider_assigned', 'picked_up', 'out_for_delivery'])
            ->latest()
            ->first();

        // Fallback: latest delivered order na may messages (para makita ang history)
        if (!$currentOrder) {
            $currentOrder = Order::with(['restaurant', 'customer', 'items', 'payment'])
                ->where('rider_id', $rider->id)
                ->where('status', 'delivered')
                ->whereHas('messages')
                ->latest()
                ->first();
        }

        // Walang redirect — hayaan pumasok sa chat page
        // Kung walang $currentOrder, magpapakita ng "No active chats" sa view
        return view('rider.chat', compact('currentOrder'));
    }
}