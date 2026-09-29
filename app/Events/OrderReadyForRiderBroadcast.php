<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderReadyForRiderBroadcast implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order) {}

    public function broadcastOn(): array
    {
        // ⭐ Sa rider.{id} channel — para sure na naka-receive ang rider dashboard
        return [new PrivateChannel("rider.{$this->order->rider_id}")];
    }

    public function broadcastAs(): string
    {
        return 'order.ready';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'restaurant_name' => $this->order->restaurant->name ?? '',
            'restaurant_address' => $this->order->restaurant->address ?? '',
            'message' => 'Food is ready! Proceed to pickup.',
        ];
    }
}