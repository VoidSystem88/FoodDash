<?php

namespace App\Services;

use App\Events\DeliveryOfferSent;
use App\Events\NoRiderAvailable;
use App\Models\DeliveryOffer;
use App\Models\Order;
use App\Models\Rider;
use App\Models\SystemConfig;
use App\Notifications\NoRiderNotification;
use Illuminate\Support\Facades\Log;

class RiderSearchService
{
    /**
     * Multi-batch radius expansion.
     * 
     * Batch 1: Radius 1km → wait 50s
     * Batch 2: Radius 2km → wait 50s
     * ...
     * Hanggang max radius o may mag-accept.
     */
    public function findRider(Order $order): void
    {
        Log::info("=== FIND RIDER START ===", [
            'order_id' => $order->id,
            'status' => $order->status,
        ]);

        if ($order->rider_id) {
            Log::info("Order #{$order->id} already has a rider, skipping.");
            return;
        }

        // Only search for confirmed/preparing orders
        $allowedStatuses = ['confirmed', 'preparing'];

        if (!in_array($order->status, $allowedStatuses)) {
            Log::info("Order #{$order->id} not searchable (status: {$order->status}). Skipping.");
            return;
        }

        $config = SystemConfig::current();
        $maxRadius = (int) $config->service_radius_km;

        $restLat = (float) $order->restaurant->latitude;
        $restLng = (float) $order->restaurant->longitude;

        $candidates = Rider::query()
            ->where('is_online', true)
            ->where('is_available', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereHas('user', fn($q) => $q->where('status', 'approved'))
            ->get();

        Log::info("Rider candidates for order #{$order->id}", [
            'total_candidates' => $candidates->count(),
            'max_radius' => $maxRadius,
            'candidate_ids' => $candidates->pluck('id')->toArray(),
        ]);

        // Track riders who already received an offer
        $alreadyOfferedRiderIds = [];

        for ($radius = 1; $radius <= $maxRadius; $radius++) {
            // Refresh order to check if someone accepted
            $order->refresh();

            if ($order->rider_id) {
                Log::info("Order #{$order->id} accepted by rider #{$order->rider_id}. Stopping search.");
                return;
            }

            // Check if order was cancelled
            if (in_array($order->status, ['cancelled', 'rejected'])) {
                Log::info("Order #{$order->id} was {$order->status}. Stopping search.");
                return;
            }

            $inner = $radius - 1;

            $eligible = $candidates->filter(function ($rider) use (
                $restLat, $restLng, $inner, $radius, $alreadyOfferedRiderIds
            ) {
                // Skip kung na-offeran na sa previous batch
                if (in_array($rider->id, $alreadyOfferedRiderIds)) {
                    return false;
                }

                $d = $this->distanceKm(
                    $restLat, $restLng,
                    (float) $rider->latitude, (float) $rider->longitude
                );

                return $d > $inner && $d <= $radius;
            });

            Log::info("Batch {$radius}km for order #{$order->id}: {$eligible->count()} eligible riders", [
                'rider_ids' => $eligible->pluck('id')->toArray(),
            ]);

            if ($eligible->isEmpty()) {
                continue;
            }

            // Mark as offered
            foreach ($eligible as $rider) {
                $alreadyOfferedRiderIds[] = $rider->id;
            }

            // Offer to this batch
            $accepted = $this->offerToRiders($order, $eligible, $radius);

            if ($accepted) {
                Log::info("✅ Rider accepted for order #{$order->id} at radius {$radius}km");
                return;
            }

            // Check kung may nag-accept during the wait
            $order->refresh();
            if ($order->rider_id) {
                Log::info("Order #{$order->id} accepted during batch {$radius}. Stopping.");
                return;
            }
        }

        // No rider found
        Log::warning("❌ No rider found for order #{$order->id} after all batches.");

        $order->update(['status' => 'no_rider']);

        broadcast(new NoRiderAvailable($order->fresh()));

        try {
            $order->customer->notify(new NoRiderNotification($order->fresh()));
        } catch (\Throwable $e) {
            Log::error('NoRider notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Offer to a batch of riders, wait for acceptance.
     */
    protected function offerToRiders(Order $order, $riders, int $radiusKm): bool
    {
        $timeout = 50; // 50 seconds per batch
        $now = now();

        // Filter out riders who already have an offer for this order
        $existingRiderIds = DeliveryOffer::where('order_id', $order->id)
            ->pluck('rider_id')
            ->toArray();

        $newRiders = $riders->filter(fn($r) => !in_array($r->id, $existingRiderIds));

        if ($newRiders->isEmpty()) {
            Log::info("All riders in this batch already have offers for order #{$order->id}");
            return false;
        }

        // Create offers
        $offerRows = $newRiders->map(fn($r) => [
            'order_id' => $order->id,
            'rider_id' => $r->id,
            'radius_km' => $radiusKm,
            'status' => 'pending',
            'expires_at' => $now->copy()->addSeconds($timeout),
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        DeliveryOffer::insert($offerRows);

        // Broadcast to each rider
        foreach ($newRiders as $rider) {
            try {
                broadcast(new DeliveryOfferSent($order, $rider, $radiusKm, $timeout));
            } catch (\Throwable $e) {
                Log::error("Broadcast failed for rider #{$rider->id}: " . $e->getMessage());
            }
        }

        Log::info("Batch {$radiusKm}km offers sent to " . count($offerRows) . " riders");

        // Wait for acceptance
        $acceptedRiderId = $this->waitForAcceptance($order->id, $timeout);

        if ($acceptedRiderId) {
            // Expire other offers
            DeliveryOffer::where('order_id', $order->id)
                ->where('rider_id', '!=', $acceptedRiderId)
                ->update(['status' => 'expired']);

            DeliveryOffer::where('order_id', $order->id)
                ->where('rider_id', $acceptedRiderId)
                ->update(['status' => 'accepted']);

            return true;
        }

        // Expire all pending offers for this batch
        DeliveryOffer::where('order_id', $order->id)
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        return false;
    }

    /**
     * Wait for a rider to accept. Check cache every 0.3s.
     */
    protected function waitForAcceptance(int $orderId, int $timeoutSec): ?int
    {
        $key = "order:{$orderId}:accepted_rider";
        $deadline = microtime(true) + $timeoutSec;

        while (microtime(true) < $deadline) {
            $riderId = cache()->get($key);
            if ($riderId) {
                cache()->forget($key);
                Log::info("Rider #{$riderId} accepted order #{$orderId}");
                return (int) $riderId;
            }
            usleep(300_000); // 0.3s
        }

        return null;
    }

    /**
     * Haversine distance in km.
     */
    public function distanceKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}