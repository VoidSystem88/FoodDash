<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderPlaced implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Order $order) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("restaurant.{$this->order->restaurant_id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new.order';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->order->id,
            'customer_name' => $this->order->customer->name ?? 'Customer',
            'items_count' => $this->order->items->count(),
            'food_cost' => (float) $this->order->food_cost,
            'delivery_fee' => (float) $this->order->delivery_fee,
            'total_amount' => (float) $this->order->total_amount,
            'delivery_address' => $this->order->delivery_address,
            'is_external' => (bool) $this->order->is_external_order,
            'created_at' => $this->order->created_at->toIso8601String(),
            'created_at_human' => $this->order->created_at->diffForHumans(),
        ];
    }
}