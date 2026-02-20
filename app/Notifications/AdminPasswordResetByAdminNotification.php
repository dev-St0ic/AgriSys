<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminPasswordResetByAdminNotification extends Notification
{
    protected $resetBy;
    protected $temporaryPassword;

    /**
     * Create a new notification instance.
     */
    public function __construct($resetBy, $temporaryPassword = null)
    {
        $this->resetBy = $resetBy;
        $this->temporaryPassword = $temporaryPassword;
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
        $message = (new MailMessage)
            ->subject('Your Password Has Been Reset - AgriSys')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is to inform you that your password has been reset by a system administrator.')
            ->line('**Reset by:** ' . $this->resetBy)
            ->line('**Date & Time:** ' . now()->format('F d, Y \a\t g:i A'));

        if ($this->temporaryPassword) {
            $message->line('**Your Temporary Password:** ' . $this->temporaryPassword)
                    ->line('**Important Security Notice:**')
                    ->line('• Please change this temporary password immediately after logging in')
                    ->line('• Do not share this password with anyone')
                    ->line('• You will be required to change your password on next login');
        } else {
            $message->line('Your password has been updated by the administrator.')
                    ->line('If you did not request this change or are unaware of it, please contact your system administrator immediately.');
        }

        $message->line('For security reasons, you have been logged out from all active sessions.')
                ->action('Login to AgriSys', url('/login'))
                ->line('If you did not authorize this password reset, please contact your system administrator immediately.')
                ->salutation('Best regards, AgriSys');

        return $message;
    }
}