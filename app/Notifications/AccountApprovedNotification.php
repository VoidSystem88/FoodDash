<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $role,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $roleLabels = [
            'customer' => 'Customer',
            'restaurant' => 'Restaurant Owner',
            'rider' => 'Delivery Rider',
            'admin' => 'Administrator',
        ];

        $roleLabel = $roleLabels[$this->role] ?? ucfirst($this->role);

        $mail = (new MailMessage)
            ->subject('🎉 Your FoodDash Account Has Been Approved!')
            ->greeting("Hi {$notifiable->name}!")
            ->line("Great news! Your **{$roleLabel}** account has been approved by our admin team.")
            ->line('')
            ->line('You can now log in and start using FoodDash.');

        // Role-specific messages
        if ($this->role === 'restaurant') {
            $mail->line('')
                ->line('**As a Restaurant Owner, you can now:**')
                ->line('• Add and manage your menu items')
                ->line('• Set your operating hours')
                ->line('• Receive and confirm customer orders')
                ->line('• Track your sales and analytics')
                ->line('• Upload your restaurant cover and profile photos');
        } elseif ($this->role === 'rider') {
            $mail->line('')
                ->line('**As a Delivery Rider, you can now:**')
                ->line('• Go online to receive delivery offers')
                ->line('• Accept orders near your location')
                ->line('• Track your earnings and delivery history')
                ->line('• Chat with customers during delivery');
        } else {
            $mail->line('')
                ->line('**As a Customer, you can now:**')
                ->line('• Browse restaurants and menus')
                ->line('• Place orders and track delivery in real-time')
                ->line('• Chat with your rider')
                ->line('• Rate restaurants and riders');
        }

        $mail->line('')
            ->action('Log In to FoodDash', url('/login'))
            ->line('')
            ->line('Welcome to the FoodDash family! 🍕')
            ->salutation('— The FoodDash Team');

        return $mail;
    }
}