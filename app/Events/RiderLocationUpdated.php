<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RiderLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $orderId,
        public float $latitude,
        public float $longitude,
        public string $riderName,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("order.{$this->orderId}")];
    }

    public function broadcastAs(): string
    {
        return 'rider.location';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->orderId,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'rider_name' => $this->riderName,
        ];
    }
}