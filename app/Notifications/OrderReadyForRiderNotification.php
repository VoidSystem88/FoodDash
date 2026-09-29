<?php

namespace App\Notifications;

use App\Events\NotificationSent;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderReadyForRiderNotification extends Notification
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
            ->subject("🍔 Order #{$this->order->id} — Ready for Pickup!")
            ->greeting("Hi {$notifiable->name}!")
            ->line("Ang order #{$this->order->id} ay ready na para i-pickup!")
            ->line("**Pickup From:**")
            ->line($this->order->restaurant->name)
            ->line($this->order->restaurant->address)
            ->action('View Order', url('/rider/dashboard'))
            ->line('Ingat sa pagmamaneho! 🛵');
    }

    public function toArray(object $notifiable): array
    {
        $title = '🍔 Food Ready!';
        $body = "Order #{$this->order->id} — pickup na sa {$this->order->restaurant->name}";

        broadcast(new NotificationSent(
            userId: $notifiable->id,
            id: (string) \Illuminate\Support\Str::uuid(),
            title: $title,
            body: $body,
            url: route('rider.dashboard'),
            icon: 'package',
            createdAt: now()->toIso8601String(),
        ));

        return [
            'title' => $title,
            'body' => $body,
            'url' => route('rider.dashboard'),
            'icon' => 'package',
            'order_id' => $this->order->id,
        ];
    }
}