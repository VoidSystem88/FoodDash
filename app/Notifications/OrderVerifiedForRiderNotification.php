<?php

namespace App\Notifications;

use App\Events\NotificationSent;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderVerifiedForRiderNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Order #{$this->order->id} — Verified! Proceed to Restaurant")
            ->greeting("Hi {$notifiable->name}!")
            ->line("The restaurant has started preparing order #{$this->order->id}.")
            ->line("You may now proceed to the restaurant to wait for pickup.")
            ->line("**Pickup From:**")
            ->line($this->order->restaurant->name)
            ->line($this->order->restaurant->address)
            ->action('View Order', url('/rider/dashboard'))
            ->line('Ingat sa pagmamaneho! 🛵');
    }

    public function toArray(object $notifiable): array
    {
        $title = '✅ Order Verified';
        $body = "Order #{$this->order->id} — proceed to {$this->order->restaurant->name}";

        broadcast(new NotificationSent(
            userId: $notifiable->id,
            id: (string) \Illuminate\Support\Str::uuid(),
            title: $title,
            body: $body,
            url: route('rider.dashboard'),
            icon: 'check-circle',
            createdAt: now()->toIso8601String(),
        ));

        return [
            'title' => $title,
            'body' => $body,
            'url' => route('rider.dashboard'),
            'icon' => 'check-circle',
            'order_id' => $this->order->id,
        ];
    }
}