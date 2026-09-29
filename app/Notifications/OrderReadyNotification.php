<?php

namespace App\Notifications;

use App\Events\NotificationSent;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderReadyNotification extends Notification
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
            ->subject("Order #{$this->order->id} — Ready for Pickup! 🎉")
            ->greeting("Hi {$notifiable->name}!")
            ->line("Ang order #{$this->order->id} mula sa **{$this->order->restaurant->name}** ay ready na!")
            ->line("Pumunta na sa restaurant para i-pickup ang order.")
            ->line("**Delivery Address:**")
            ->line($this->order->delivery_address)
            ->action('View Order', url("/rider/dashboard"))
            ->line('Ingat sa pagmamaneho! 🛵');
    }

    public function toArray(object $notifiable): array
    {
        $title = "Order #{$this->order->id} — Ready for Pickup";
        $body = "Ready na ang order sa {$this->order->restaurant->name}. Pumunta na!";

        broadcast(new NotificationSent(
            userId: $notifiable->id,
            id: (string) \Illuminate\Support\Str::uuid(),
            title: $title,
            body: $body,
            url: route('rider.dashboard'),
            icon: 'truck',
            createdAt: now()->toIso8601String(),
        ));

        return [
            'title' => $title,
            'body' => $body,
            'url' => route('rider.dashboard'),
            'icon' => 'truck',
            'order_id' => $this->order->id,
            'restaurant_id' => $this->order->restaurant_id,
        ];
    }
}