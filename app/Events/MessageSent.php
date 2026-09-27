<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Message $message) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("order.{$this->message->order_id}.chat")];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        try {
            // Load sender kung hindi pa naka-load
            $sender = $this->message->sender;

            if (!$sender) {
                Log::warning('MessageSent: Sender not loaded', [
                    'message_id' => $this->message->id,
                    'sender_id' => $this->message->sender_id,
                ]);

                // Fallback: force load
                $sender = \App\Models\User::find($this->message->sender_id);
            }

            return [
                'id' => $this->message->id,
                'order_id' => $this->message->order_id,
                'sender_id' => $this->message->sender_id,
                'sender_name' => $sender?->name ?? 'Unknown',
                'sender_role' => $sender?->role ?? null,
                'sender_avatar_url' => $sender?->avatar_url ?? null,
                'sender_initials' => $sender?->initials ?? '?',
                'sender_avatar_color' => $sender?->avatar_color ?? 'bg-gray-500',
                'body' => $this->message->body,
                'created_at' => $this->message->created_at?->toIso8601String(),
                'created_at_human' => $this->message->created_at?->diffForHumans() ?? 'now',
                'delivered_at' => $this->message->delivered_at?->toIso8601String(),
                'read_at' => $this->message->read_at?->toIso8601String(),
            ];
        } catch (\Throwable $e) {
            Log::error('MessageSent broadcastWith FAILED', [
                'message_id' => $this->message->id ?? 'unknown',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Minimal fallback para hindi mag-fail ang broadcast
            return [
                'id' => $this->message->id,
                'order_id' => $this->message->order_id,
                'sender_id' => $this->message->sender_id,
                'sender_name' => 'Unknown',
                'body' => $this->message->body,
            ];
        }
    }
}