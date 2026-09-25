<?php

namespace App\Notifications;

use App\Events\NotificationSent;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewChatMessageNotification extends Notification
{
    use Queueable;

    public function __construct(public Message $message) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $sender = $this->message->sender->name;
        $title = "New message from {$sender}";
        $body = \Illuminate\Support\Str::limit($this->message->body, 60);

        $url = $this->message->order->customer_id === $notifiable->id
            ? route('customer.orders.show', $this->message->order)
            : route('rider.dashboard');

        broadcast(new NotificationSent(
            userId: $notifiable->id,
            id: (string) \Illuminate\Support\Str::uuid(),
            title: $title,
            body: $body,
            url: $url,
            icon: '💬',
            createdAt: now()->toIso8601String(),
        ));

        return [
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'icon' => '💬',
            'order_id' => $this->message->order_id,
            'message_id' => $this->message->id,
        ];
    }
}