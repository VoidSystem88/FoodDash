<?php

namespace App\Notifications;

use App\Events\NotificationSent;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NoRiderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Order #{$this->order->id} — No Rider Available")
            ->greeting("Hi {$notifiable->name}!")
            ->line("Unfortunately, no rider is available for your order #{$this->order->id}.")
            ->line("The restaurant has been notified. You can wait or cancel the order.")
            ->action('View Order', url("/orders/{$this->order->id}"))
            ->line('We apologize for the inconvenience.');
    }

    public function toArray(object $notifiable): array
    {
        $title = 'No Rider Available';
        $body = "Unfortunately, no rider is available for order #{$this->order->id}.";

        broadcast(new NotificationSent(
            userId: $notifiable->id,
            id: (string) \Illuminate\Support\Str::uuid(),
            title: $title,
            body: $body,
            url: route('customer.orders.show', $this->order),
            icon: '😔',
            createdAt: now()->toIso8601String(),
        ));

        return [
            'title' => $title,
            'body' => $body,
            'url' => route('customer.orders.show', $this->order),
            'icon' => '😔',
            'order_id' => $this->order->id,
        ];
    }
}