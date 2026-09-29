<?php

namespace App\Notifications;

use App\Events\NotificationSent;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderCancelledForRiderNotification extends Notification
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
            ->subject("❌ Order #{$this->order->id} Cancelled")
            ->greeting("Hi {$notifiable->name}!")
            ->line("Ang order #{$this->order->id} ay na-cancel ng customer.")
            ->line("Reason: {$this->order->cancellation_reason}")
            ->line("You are now free to accept other orders.")
            ->action('View Dashboard', url('/rider/dashboard'));
    }

    public function toArray(object $notifiable): array
    {
        $title = '❌ Order Cancelled';
        $body = "Order #{$this->order->id} — cancelled by customer";

        broadcast(new NotificationSent(
            userId: $notifiable->id,
            id: (string) \Illuminate\Support\Str::uuid(),
            title: $title,
            body: $body,
            url: route('rider.dashboard'),
            icon: 'x-circle',
            createdAt: now()->toIso8601String(),
        ));

        return [
            'title' => $title,
            'body' => $body,
            'url' => route('rider.dashboard'),
            'icon' => 'x-circle',
            'order_id' => $this->order->id,
        ];
    }
}