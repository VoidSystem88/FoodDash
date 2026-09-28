<?php

namespace App\Notifications;

use App\Events\NotificationSent;
use App\Models\ReviewReply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewRepliedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ReviewReply $reply) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $restaurantName = $this->reply->review->restaurant->name;

        return (new MailMessage)
            ->subject("{$restaurantName} replied to your review")
            ->greeting("Hi {$notifiable->name}!")
            ->line("**{$restaurantName}** replied to your review.")
            ->line('')
            ->line('Their reply:')
            ->line('"' . $this->reply->body . '"')
            ->line('')
            ->action('View Reply', route('customer.orders.show', $this->reply->review->order_id))
            ->line('Thank you for your feedback!')
            ->salutation('— The FoodDash Team');
    }

    public function toArray(object $notifiable): array
    {
        $restaurantName = $this->reply->review->restaurant->name;
        $title = "{$restaurantName} replied to your review";
        $body = \Illuminate\Support\Str::limit($this->reply->body, 60);

        // Broadcast real-time
        if ($notifiable->id) {
            broadcast(new NotificationSent(
                userId: $notifiable->id,
                id: (string) \Illuminate\Support\Str::uuid(),
                title: $title,
                body: $body,
                url: route('customer.orders.show', $this->reply->review->order_id),
                icon: 'chat',
                createdAt: now()->toIso8601String(),
            ));
        }

        return [
            'title' => $title,
            'body' => $body,
            'url' => route('customer.orders.show', $this->reply->review->order_id),
            'icon' => 'chat',
            'review_id' => $this->reply->review_id,
            'reply_id' => $this->reply->id,
        ];
    }
}