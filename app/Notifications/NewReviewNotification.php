<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification
{
    use Queueable;

    public function __construct(public Review $review) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $stars = str_repeat('⭐', $this->review->rating);
        $title = "New review for {$this->review->restaurant->name}";

        return [
            'title' => $title,
            'body' => "{$stars} — {$this->review->user->name}",
            'url' => route('restaurant.dashboard'),
            'icon' => '⭐',
            'review_id' => $this->review->id,
        ];
    }
}