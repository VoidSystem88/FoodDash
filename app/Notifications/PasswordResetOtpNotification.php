<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetOtpNotification extends Notification
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
            ->subject('Reset Your FoodDash Password')
            ->greeting("Hi {$notifiable->name}!")
            ->line('Use the code below to reset your password:')
            ->line('')
            ->line("## **{$this->otp}**")
            ->line('')
            ->line('This code will expire in **10 minutes**.')
            ->line('If you did not request a password reset, no further action is required.')
            ->salutation('— The FoodDash Team');
    }
}