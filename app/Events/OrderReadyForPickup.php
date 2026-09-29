<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderReadyForPickup implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("order.{$this->order->id}")];
    }

    public function broadcastAs(): string
    {
        return 'order.ready.for.pickup';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => 'ready_for_pickup',
            'restaurant_name' => $this->order->restaurant->name ?? '',
            'message' => 'Ready na ang order! Pumunta ka na sa restaurant.',
        ];
    }
}