<?php

namespace App\Notifications;

use App\Events\NotificationSent;
use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Review $review) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $stars = str_repeat('⭐', $this->review->rating);

        $mail = (new MailMessage)
            ->subject("New Review for {$this->review->restaurant->name}")
            ->greeting("Hi {$notifiable->name}!")
            ->line("You received a new review from **{$this->review->user->name}**.")
            ->line('')
            ->line("Rating: {$stars} ({$this->review->rating}/5)");

        if ($this->review->title) {
            $mail->line("Title: **{$this->review->title}**");
        }

        $mail->line('')
            ->line('Review:')
            ->line('"' . \Illuminate\Support\Str::limit($this->review->body, 200) . '"')
            ->line('')
            ->action('View & Reply', route('restaurant.reviews.index'))
            ->line('You can reply to this review to show your appreciation or address concerns.')
            ->salutation('— The FoodDash Team');

        return $mail;
    }

    public function toArray(object $notifiable): array
    {
        $stars = str_repeat('⭐', $this->review->rating);
        $title = "New review from {$this->review->user->name}";
        $body = "{$stars} — " . \Illuminate\Support\Str::limit($this->review->body, 60);

        // Broadcast real-time
        if ($notifiable->id) {
            broadcast(new NotificationSent(
                userId: $notifiable->id,
                id: (string) \Illuminate\Support\Str::uuid(),
                title: $title,
                body: $body,
                url: route('restaurant.reviews.index'),
                icon: 'star',
                createdAt: now()->toIso8601String(),
            ));
        }

        return [
            'title' => $title,
            'body' => $body,
            'url' => route('restaurant.reviews.index'),
            'icon' => 'star',
            'review_id' => $this->review->id,
            'restaurant_id' => $this->review->restaurant_id,
        ];
    }
}