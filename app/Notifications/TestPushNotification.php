<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TestPushNotification extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('FoodDash Test Notification 🎉')
            ->body('Push notifications are working! You will receive order updates here.')
            ->icon('/icon-192.png')
            ->badge('/icon-192.png')
            ->tag('fooddash-test')
            ->data(['url' => route('profile.index')])
            ->options(['TTL' => 3600]);
    }
}