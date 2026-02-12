<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyAdminEmail extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Verify Your Email Address - AgriSys')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Please verify your email address to activate access to your AgriSys account.')
            ->action('Verify Email Address', $verificationUrl)
            ->line('This link will expire in 60 minutes.')
            ->line('If you did not request this, no further action is required.')
            ->salutation('Best regards, AgriSys');
    }
}