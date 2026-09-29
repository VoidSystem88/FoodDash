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
    public function findRider(Order $order): void
    {
        if ($order->rider_id) {
            Log::info("Order #{$order->id} already has a rider, skipping search.");
            return;
        }

        // ⭐ Allow 'confirmed', 'preparing', at 'ready_for_pickup'
        // para sa advance booking flow
        $allowedStatuses = ['confirmed', 'preparing', 'ready_for_pickup', 'finding_rider'];

        if (!in_array($order->status, $allowedStatuses)) {
            Log::info("Order #{$order->id} is not in a searchable status (status: {$order->status}). Skipping search.");
            return;
        }

        // Guard: Huwag mag-search kung finalized na ang status
        if (in_array($order->status, ['delivered', 'cancelled', 'rejected', 'no_rider'])) {
            Log::info("Order #{$order->id} is already {$order->status}, skipping search.");
            return;
        }

        $config = SystemConfig::current();
        $maxRadius = (int) $config->service_radius_km;

        $restLat = (float) $order->restaurant->latitude;
        $restLng = (float) $order->restaurant->longitude;

        // ⭐ HUWAG i-override ang status — i-keep ang 'confirmed' o 'preparing'
        // 'finding_rider' status ay legacy na at hindi na ginagamit sa bagong flow
        // Ang rider search ay pwedeng mag-run nang hindi binabago ang order status

        $candidates = Rider::query()
            ->where('is_online', true)
            ->where('is_available', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereHas('user', fn($q) => $q->where('status', 'approved'))
            ->get();

        Log::info("Searching riders for order #{$order->id}", [
            'max_radius' => $maxRadius,
            'total_candidates' => $candidates->count(),
            'order_status' => $order->status,
        ]);

        for ($radius = 1; $radius <= $maxRadius; $radius++) {
            $inner = $radius - 1;

            $eligible = $candidates->filter(function ($rider) use ($restLat, $restLng, $inner, $radius) {
                $d = $this->distanceKm(
                    $restLat,
                    $restLng,
                    (float) $rider->latitude,
                    (float) $rider->longitude
                );
                return $d > $inner && $d <= $radius;
            });

            Log::info("Radius {$radius}km: {$eligible->count()} eligible riders");

            if ($eligible->isEmpty()) {
                continue;
            }

            $accepted = $this->offerToRiders($order, $eligible, $radius);
            if ($accepted) {
                return;
            }
        }

        // ⭐ HUWAG i-set sa 'no_rider' — i-keep ang original status
        // para pwedeng mag-retry sa susunod na restaurant action
        // Ang 'no_rider' ay dapat i-set lang kapag terminal state na

        // ⭐ I-notify ang customer pero hindi babaguhin ang status
        $order->customer->notify(new NoRiderNotification($order->fresh()));

        Log::warning("No rider found for order #{$order->id}. Keeping status: {$order->status}");
    }

    protected function offerToRiders(Order $order, $riders, int $radiusKm): bool
    {
        $timeout = 60;
        $now = now();

        // I-check kung may existing pending offers
        $existingOffers = DeliveryOffer::where('order_id', $order->id)
            ->whereIn('rider_id', $riders->pluck('id'))
            ->where('status', 'pending')
            ->where('expires_at', '>', $now)
            ->exists();

        if ($existingOffers) {
            Log::info("Existing pending offers found for order #{$order->id}, skipping duplicate insert.");
            return false;
        }

        // Filter out riders na may existing record na
        $existingRiderIds = DeliveryOffer::where('order_id', $order->id)
            ->pluck('rider_id')
            ->toArray();

        $newRiders = $riders->filter(fn($r) => !in_array($r->id, $existingRiderIds));

        if ($newRiders->isEmpty()) {
            Log::info("All riders already have offers for order #{$order->id}");
            return false;
        }

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

        foreach ($newRiders as $rider) {
            broadcast(new DeliveryOfferSent($order, $rider, $radiusKm, $timeout));
        }

        $acceptedRiderId = $this->waitForAcceptance($order->id, $timeout);

        if ($acceptedRiderId) {
            DeliveryOffer::where('order_id', $order->id)
                ->where('rider_id', '!=', $acceptedRiderId)
                ->update(['status' => 'expired']);

            DeliveryOffer::where('order_id', $order->id)
                ->where('rider_id', $acceptedRiderId)
                ->update(['status' => 'accepted']);

            Rider::where('id', $acceptedRiderId)->update(['is_available' => false]);

            return true;
        }

        DeliveryOffer::where('order_id', $order->id)
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        return false;
    }

    protected function waitForAcceptance(int $orderId, int $timeoutSec): ?int
    {
        $key = "order:{$orderId}:accepted_rider";
        $deadline = microtime(true) + $timeoutSec;

        while (microtime(true) < $deadline) {
            $riderId = cache()->get($key);
            if ($riderId) {
                cache()->forget($key);
                return (int) $riderId;
            }
            usleep(300_000);   // check every 0.3s
        }

        return null;
    }

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