<?php

namespace App\Http\Controllers\Rider;

use App\Events\OrderStatusUpdated;
use App\Events\RiderAssigned;
use App\Http\Controllers\Controller;
use App\Models\DeliveryOffer;
use App\Models\Order;
use App\Models\Payment;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * ⭐ Get offer details para sa global popup notification
     */
    public function showOffer(Request $request, Order $order)
    {
        $rider = $request->user()->rider;
        abort_unless($rider, 403, 'Not a rider');

        // Check kung may pending offer para sa rider na ito
        $offer = DeliveryOffer::where('order_id', $order->id)
            ->where('rider_id', $rider->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if (!$offer) {
            return response()->json([
                'ok' => false,
                'message' => 'Offer expired or not found.',
            ], 404);
        }

        // Load relationships
        $order->load(['restaurant', 'customer', 'items']);

        // Calculate distance (para ipakita sa rider)
        $distanceKm = null;
        if ($rider->latitude && $rider->longitude && $order->restaurant) {
            $distanceKm = $this->calculateDistance(
                (float) $rider->latitude,
                (float) $rider->longitude,
                (float) $order->restaurant->latitude,
                (float) $order->restaurant->longitude
            );
        }

        return response()->json([
            'ok' => true,
            'offer' => [
                'order_id' => $order->id,
                'radius_km' => $offer->radius_km,
                'expires_in' => now()->diffInSeconds($offer->expires_at),
                'restaurant' => $order->restaurant->name,
                'restaurant_address' => $order->restaurant->address,
                'delivery_address' => $order->delivery_address,
                'food_cost' => $order->food_cost,
                'delivery_fee' => $order->delivery_fee,
                'total_amount' => $order->total_amount,
                'items_count' => $order->items->count(),
                'distance_km' => $distanceKm ? round($distanceKm, 2) : null,
            ],
        ]);
    }

    public function accept(Request $request, Order $order)
    {
        $rider = $request->user()->rider;
        abort_unless($rider && $rider->is_online, 403, 'Rider not online');

        $offer = DeliveryOffer::where('order_id', $order->id)
            ->where('rider_id', $rider->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if (!$offer) {
            return response()->json([
                'ok' => false,
                'message' => 'Offer expired or not found.',
            ], 422);
        }

        $updated = Order::where('id', $order->id)
            ->whereNull('rider_id')
            ->whereIn('status', ['finding_rider', 'confirmed'])
            ->update([
                'rider_id' => $rider->id,
                'status' => 'rider_assigned',
            ]);

        if ($updated === 0) {
            return response()->json([
                'ok' => false,
                'message' => 'Another rider accepted first.',
            ], 422);
        }

        cache()->put("order:{$order->id}:accepted_rider", $rider->id, 60);
        $rider->update(['is_available' => false]);

        $order->refresh();

        broadcast(new RiderAssigned($order));

        // Notify customer
        $order->customer->notify(new OrderStatusNotification($order->fresh()));

        return response()->json([
            'ok' => true,
            'message' => 'Order accepted! Proceed to the restaurant.',
            'redirect' => route('rider.dashboard'),
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:picked_up,out_for_delivery,delivered',
        ]);

        $rider = $request->user()->rider;
        abort_unless($order->rider_id === $rider->id, 403);

        $order->update(['status' => $data['status']]);

        // Notify customer
        $order->customer->notify(new OrderStatusNotification($order->fresh()));

        if ($data['status'] === 'delivered') {
            $rider->update(['is_available' => true]);
        }

        broadcast(new OrderStatusUpdated($order->fresh()));

        $label = str_replace('_', ' ', $data['status']);

        return redirect()->route('rider.dashboard')
            ->with('success', "Status updated to: " . ucfirst($label));
    }

    public function recordPayment(Request $request, Order $order)
    {
        $rider = $request->user()->rider;
        abort_unless($order->rider_id === $rider->id, 403);

        if ($order->payment()->exists()) {
            return redirect()->route('rider.dashboard')
                ->with('error', 'Payment already recorded.');
        }

        Payment::create([
            'order_id' => $order->id,
            'rider_paid_restaurant' => $order->restaurant_earnings ?? $order->food_cost,
            'rider_collected_customer' => $order->total_amount,
            'recorded_at' => now(),
        ]);

        return redirect()->route('rider.dashboard')
            ->with('success', 'Payment recorded successfully!');
    }

    /**
     * Calculate distance between two coordinates (km)
     */
    protected function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R = 6371; // Earth radius in km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
        /**
     * Get all active offers for this rider
     */
    public function activeOffers(Request $request)
    {
        $rider = $request->user()->rider;
        abort_unless($rider, 403);

        $offers = DeliveryOffer::where('rider_id', $rider->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->with(['order.restaurant', 'order.items'])
            ->get()
            ->map(function ($offer) use ($rider) {
                $order = $offer->order;

                // Calculate distance
                $distanceKm = null;
                if ($rider->latitude && $rider->longitude && $order->restaurant) {
                    $distanceKm = $this->calculateDistance(
                        (float) $rider->latitude,
                        (float) $rider->longitude,
                        (float) $order->restaurant->latitude,
                        (float) $order->restaurant->longitude
                    );
                }

                return [
                    'order_id' => $order->id,
                    'radius_km' => $offer->radius_km,
                    'expires_in' => now()->diffInSeconds($offer->expires_at),
                    'secondsLeft' => now()->diffInSeconds($offer->expires_at),
                    'restaurant' => $order->restaurant->name,
                    'restaurant_address' => $order->restaurant->address,
                    'delivery_address' => $order->delivery_address,
                    'food_cost' => $order->food_cost,
                    'delivery_fee' => $order->delivery_fee,
                    'total_amount' => $order->total_amount,
                    'items_count' => $order->items->count(),
                    'distance_km' => $distanceKm ? round($distanceKm, 2) : null,
                ];
            });

        return response()->json(['offers' => $offers]);
    }
}