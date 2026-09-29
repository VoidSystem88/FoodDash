<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderVerifiedForRider implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("order.{$this->order->id}")];
    }

    public function broadcastAs(): string
    {
        return 'order.verified.for.rider';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->order->status,
            'restaurant_name' => $this->order->restaurant->name ?? '',
            'restaurant_address' => $this->order->restaurant->address ?? '',
            'message' => 'Order verified! Proceed to restaurant.',
        ];
    }
}