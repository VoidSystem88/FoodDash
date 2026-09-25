<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpVerificationNotification extends Notification
{
    use Queueable;

    public function __construct(public string $otp) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your FoodDash Verification Code')
            ->greeting("Hi {$notifiable->name}!")
            ->line('Use the code below to verify your email address:')
            ->line('')
            ->line("## **{$this->otp}**")
            ->line('')
            ->line('This code will expire in **10 minutes**.')
            ->line('If you did not create an account, no further action is required.')
            ->salutation('— The FoodDash Team');
    }
}