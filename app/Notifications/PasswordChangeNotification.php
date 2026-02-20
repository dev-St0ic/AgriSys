<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class PasswordChangeNotification extends Notification
{
    protected $token;
    protected $newPassword;

    /**
     * Create a new notification instance.
     */
    public function __construct($token, $newPassword)
    {
        $this->token = $token;
        $this->newPassword = $newPassword;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'password.change.verify',
            now()->addMinutes(60),
            [
                'user' => $notifiable->id,
                'token' => $this->token
            ]
        );

        return (new MailMessage)
            ->subject('Verify Password Change - AgriSys')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have requested to change your password. Please verify this change by clicking the button below.')
            ->line('**Important:** Your password will only be changed after you verify this request.')
            ->action('Verify Password Change', $verificationUrl)
            ->line('This verification link will expire in 60 minutes.')
            ->line('If you did not request a password change, please ignore this email and your password will remain unchanged.')
            ->salutation('Best regards, AgriSys');
    }
}