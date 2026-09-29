<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Rider;
use App\Services\RiderSearchService;
use Illuminate\Console\Command;

class TestOrderFlow extends Command
{
    protected $signature = 'test:order-flow {order_id}';
    protected $description = 'Test the rider search flow for an order';

    public function handle()
    {
        $orderId = $this->argument('order_id');
        $order = Order::with(['restaurant', 'customer', 'rider.user', 'items'])->find($orderId);

        if (!$order) {
            $this->error("Order #{$orderId} not found");
            return 1;
        }

        $this->info("=== ORDER #{$order->id} ===");
        $this->line("Status: {$order->status}");
        $this->line("Restaurant: {$order->restaurant->name}");
        $this->line("Rider: " . ($order->rider?->user?->name ?? 'None'));
        $this->line("Customer: {$order->customer->name}");
        $this->line("");

        $config = \App\Models\SystemConfig::current();
        $this->line("Service Radius: {$config->service_radius_km}km");
        $this->line("");

        // List eligible riders
        $riders = Rider::with('user')
            ->where('is_online', true)
            ->where('is_available', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $this->info("=== ELIGIBLE RIDERS ===");

        if ($riders->isEmpty()) {
            $this->warn("No eligible riders found!");
            return 0;
        }

        $service = new RiderSearchService();
        $restLat = (float) $order->restaurant->latitude;
        $restLng = (float) $order->restaurant->longitude;

        foreach ($riders as $rider) {
            $distance = $service->distanceKm(
                $restLat,
                $restLng,
                (float) $rider->latitude,
                (float) $rider->longitude
            );

            $this->line(sprintf(
                "Rider #%d (%s): %.2f km — %s",
                $rider->id,
                $rider->user->name,
                $distance,
                $distance <= $config->service_radius_km ? '✅ IN RANGE' : '❌ OUT OF RANGE'
            ));
        }

        $this->line("");
        $this->info("=== DELIVERY OFFERS ===");

        $offers = \App\Models\DeliveryOffer::with('rider.user')
            ->where('order_id', $order->id)
            ->latest()
            ->get();

        if ($offers->isEmpty()) {
            $this->warn("No delivery offers yet");
        } else {
            foreach ($offers as $offer) {
                $this->line(sprintf(
                    "Offer #%d → Rider #%d (%s): %s (radius %dkm, expires %s)",
                    $offer->id,
                    $offer->rider_id,
                    $offer->rider->user->name ?? 'Unknown',
                    strtoupper($offer->status),
                    $offer->radius_km,
                    $offer->expires_at->diffForHumans()
                ));
            }
        }

        return 0;
    }
}