<?php

namespace App\Notifications;

use App\Events\NotificationSent;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $labels = [
            'confirmed' => 'Order Confirmed',
            'preparing' => 'Preparing Your Food',
            'finding_rider' => 'Finding a Rider',
            'rider_assigned' => 'Rider Assigned',
            'picked_up' => 'Order Picked Up',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Order Delivered',
            'cancelled' => 'Order Cancelled',
            'rejected' => 'Order Rejected',
            'no_rider' => 'No Rider Available',
        ];

        $label = $labels[$this->order->status]
            ?? ucfirst(str_replace('_', ' ', $this->order->status));

        $mail = (new MailMessage)
            ->subject("Order #{$this->order->id} — {$label}")
            ->greeting("Hi {$notifiable->name}!")
            ->line("Your order from **{$this->order->restaurant->name}** is now: **{$label}**");

        if ($this->order->status === 'rejected' && $this->order->rejection_reason) {
            $mail->line("Reason: {$this->order->rejection_reason}");
        }

        if ($this->order->status === 'cancelled' && $this->order->cancellation_reason) {
            $mail->line("Reason: {$this->order->cancellation_reason}");
        }

        if ($this->order->status === 'rider_assigned' && $this->order->rider) {
            $mail->line("Rider: **{$this->order->rider->user->name}**");
        }

        if (!in_array($this->order->status, ['cancelled', 'rejected', 'no_rider'])) {
            $mail->action('Track Order', url("/orders/{$this->order->id}"));
        }

        if ($this->order->status === 'delivered') {
            $mail->line('Thank you for ordering with FoodDash!');
        }

        if ($this->order->status === 'cancelled') {
            $mail->line('We hope to serve you again soon.');
        }

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        $labels = [
            'confirmed' => 'Order Confirmed',
            'preparing' => 'Preparing Your Food',
            'finding_rider' => 'Finding a Rider',
            'rider_assigned' => 'Rider Assigned',
            'picked_up' => 'Order Picked Up',
            'out_for_delivery' => 'Out for Delivery',
            'delivered' => 'Order Delivered',
            'cancelled' => 'Order Cancelled',
            'rejected' => 'Order Rejected',
            'no_rider' => 'No Rider Available',
        ];

        $icons = [
            'confirmed' => '✅',
            'preparing' => '👨‍🍳',
            'finding_rider' => '🔍',
            'rider_assigned' => '🛵',
            'picked_up' => '📦',
            'out_for_delivery' => '🚀',
            'delivered' => '🎉',
            'cancelled' => '❌',
            'rejected' => '⚠️',
            'no_rider' => '😔',
        ];

        $label = $labels[$this->order->status] ?? 'Order Update';
        $icon = $icons[$this->order->status] ?? '📢';

        $body = "Order #{$this->order->id} — {$label}";
        if ($this->order->status === 'rider_assigned' && $this->order->rider) {
            $body .= " ({$this->order->rider->user->name})";
        }

        // Broadcast in real-time
        if ($notifiable->id) {
            broadcast(new NotificationSent(
                userId: $notifiable->id,
                id: (string) \Illuminate\Support\Str::uuid(),
                title: $label,
                body: $body,
                url: route('customer.orders.show', $this->order),
                icon: $icon,
                createdAt: now()->toIso8601String(),
            ));
        }

        return [
            'title' => $label,
            'body' => $body,
            'url' => route('customer.orders.show', $this->order),
            'icon' => $icon,
            'order_id' => $this->order->id,
        ];
    }
}