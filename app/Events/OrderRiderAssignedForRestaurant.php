<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderRiderAssignedForRestaurant implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("restaurant.{$this->order->restaurant_id}")];
    }

    public function broadcastAs(): string
    {
        return 'order.rider.assigned';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'rider_id' => $this->order->rider_id,
            'rider_name' => $this->order->rider?->user?->name ?? 'Rider',
            'restaurant_id' => $this->order->restaurant_id,
        ];
    }
}