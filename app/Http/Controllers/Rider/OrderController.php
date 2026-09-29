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

        // ⭐ BAGO: I-check kung valid pa ang order
        if (!in_array($order->status, ['confirmed', 'preparing', 'ready_for_pickup'])) {
            return response()->json([
                'ok' => false,
                'message' => 'Order is no longer available.',
            ], 404);
        }

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

    /**
     * ⭐ ACCEPT ORDER — advance booking o ready_for_pickup
     */
    public function accept(Request $request, Order $order)
    {
        $rider = $request->user()->rider;
        abort_unless($rider && $rider->is_online, 403, 'Rider not online');

        // ⭐ Allow accept kahit confirmed pa (advance booking)
        $allowedStatuses = ['confirmed', 'preparing', 'ready_for_pickup'];

        if (!in_array($order->status, $allowedStatuses)) {
            return response()->json([
                'ok' => false,
                'message' => 'Hindi pa pwede i-accept ang order na ito. Status: ' . $order->status,
            ], 422);
        }

        // ⭐ Kung may rider na — bawal
        if ($order->rider_id) {
            return response()->json([
                'ok' => false,
                'message' => 'May rider na ang order na ito.',
            ], 422);
        }

        // ⭐ Assign rider — advance booking o ready
        $updated = Order::where('id', $order->id)
            ->whereNull('rider_id')
            ->whereIn('status', $allowedStatuses)
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

        // ⭐ Notify restaurant na may rider nang naka-assign
        if ($order->restaurant && $order->restaurant->user) {
            $order->restaurant->user->notify(new OrderStatusNotification($order->fresh()));
        }

        // ⭐ Dynamic message base sa status
        $message = $order->restaurant_marked_ready_at
            ? 'Order accepted! Ready na — pumunta ka na sa restaurant.'
            : 'Order accepted! Hintayin ang notification kapag ready na ang pagkain.';

        return response()->json([
            'ok' => true,
            'message' => $message,
            'redirect' => route('rider.dashboard'),
        ]);
    }

    /**
     * ⭐ UPDATE STATUS — picked_up, out_for_delivery, delivered
     */
    public function updateStatus(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:picked_up,out_for_delivery,delivered',
        ]);

        $rider = $request->user()->rider;
        abort_unless($order->rider_id === $rider->id, 403);

        // ⭐ BAGO: Validation bago mag-mark as picked_up
        if ($data['status'] === 'picked_up') {

            // ⭐ Siguraduhing ready na ang restaurant bago payagan ang pickup
            if (!$order->restaurant_marked_ready_at) {
                return back()->with('error',
                    'Hindi pa ready ang order. Hintayin ang notification mula sa restaurant.'
                );
            }

            // ⭐ Siguraduhing 'rider_assigned' pa ang status
            if ($order->status !== 'rider_assigned') {
                return back()->with('error',
                    'Invalid status transition. Current: ' . $order->status
                );
            }

            // Optional: distance check
            $distance = null;
            if ($rider->latitude && $rider->longitude && $order->restaurant) {
                $service = new \App\Services\RiderSearchService();
                $distance = $service->distanceKm(
                    (float) $rider->latitude,
                    (float) $rider->longitude,
                    (float) $order->restaurant->latitude,
                    (float) $order->restaurant->longitude
                );

                // ⭐ Optional: Mag-warning kung malayo pa sa restaurant (>500m)
                if ($distance > 0.5) {
                    \Log::info("Rider #{$rider->id} marking picked_up from {$distance}km away");
                }
            }

            // ⭐ Naka-set na verified_pickup_at
            $order->update([
                'status' => 'picked_up',
                'verified_pickup_at' => now(),
            ]);

            // Notify restaurant na nakuha na ng rider ang order
            if ($order->restaurant && $order->restaurant->user) {
                $order->restaurant->user->notify(
                    new OrderStatusNotification($order->fresh())
                );
            }
        } elseif ($data['status'] === 'out_for_delivery') {
            // ⭐ Dapat picked_up pa lang bago maging out_for_delivery
            if ($order->status !== 'picked_up') {
                return back()->with('error',
                    'Order must be "picked_up" first. Current: ' . $order->status
                );
            }

            $order->update(['status' => 'out_for_delivery']);

        } elseif ($data['status'] === 'delivered') {
            // ⭐ Dapat out_for_delivery pa lang bago maging delivered
            if ($order->status !== 'out_for_delivery') {
                return back()->with('error',
                    'Order must be "out_for_delivery" first. Current: ' . $order->status
                );
            }

            $order->update(['status' => 'delivered']);
        }

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

    /**
     * ⭐ RECORD PAYMENT
     */
    public function recordPayment(Request $request, Order $order)
    {
        $rider = $request->user()->rider;
        abort_unless($order->rider_id === $rider->id, 403);

        // ⭐ BAGO: Siguraduhing 'delivered' na ang order
        if ($order->status !== 'delivered') {
            return redirect()->route('rider.dashboard')
                ->with('error', 'Cannot record payment — order is not yet delivered.');
        }

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
            ->filter(function ($offer) {
                // ⭐ BAGO: I-filter ang mga order na hindi na valid
                return in_array($offer->order->status, [
                    'confirmed',
                    'preparing',
                    'ready_for_pickup',
                ]);
            })
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
            })
            ->values();  // ⭐ Reset keys after filter

        return response()->json(['offers' => $offers]);
    }
}