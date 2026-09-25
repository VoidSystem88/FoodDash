<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessagesRead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $orderId,
        public int $readerId,
        public array $messageIds,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("order.{$this->orderId}.chat")];
    }

    public function broadcastAs(): string
    {
        return 'messages.read';
    }

    public function broadcastWith(): array
    {
        return [
            'order_id' => $this->orderId,
            'reader_id' => $this->readerId,
            'message_ids' => $this->messageIds,
            'read_at' => now()->toIso8601String(),
        ];
    }
}