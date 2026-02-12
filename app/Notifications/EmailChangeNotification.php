<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use App\Models\User;

class EmailChangeNotification extends Notification
{
    protected $oldEmail;
    protected $newEmail;
    protected $changedBy;
    protected $token;
    protected $type;
    protected $user; // ← ADD THIS

    /**
     * @param User   $user      The actual user model (needed for id/name when using anonymous notifiable)
     * @param string $oldEmail
     * @param string $newEmail
     * @param string $changedBy
     * @param string|null $token
     * @param string $type 'confirmation' | 'notification'
     */
    public function __construct(User $user, $oldEmail, $newEmail, $changedBy = 'yourself', $token = null, $type = 'confirmation')
    {
        $this->user      = $user;   
        $this->oldEmail  = $oldEmail;
        $this->newEmail  = $newEmail;
        $this->changedBy = $changedBy;
        $this->token     = $token;
        $this->type      = $type;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->type === 'confirmation') {
            return $this->confirmationEmail();
        }

        return $this->notificationEmail();
    }

    /**
     * Email sent to OLD email address with confirmation link
     */
    private function confirmationEmail(): MailMessage
    {
        // Use $this->user instead of $notifiable — avoids null errors on anonymous notifiable
        $confirmationUrl = URL::temporarySignedRoute(
            'email.change.confirm',
            now()->addHours(24),
            [
                'user'  => $this->user->id,   // ← was $notifiable->id
                'token' => $this->token
            ]
        );

        return (new MailMessage)
            ->subject('Confirm Email Address Change - AgriSys')
            ->greeting('Hello ' . $this->user->name . '!')   // ← was $notifiable->name
            ->line('A request has been made to change your email address.')
            ->line('This email change was requested by: **' . $this->changedBy . '**')
            ->line('**Important Details:**')
            ->line('• **Current Email (This account):** ' . $this->oldEmail)
            ->line('• **New Email (Will be changed to):** ' . $this->newEmail)
            ->line('• **Requested at:** ' . now()->format('F d, Y \\a\\t g:i A'))
            ->line('')
            ->line('**To confirm this email change, click the button below:**')
            ->action('Confirm Email Change', $confirmationUrl)
            ->line('')
            ->line('**This confirmation link will expire in 24 hours.**')
            ->line('')
            ->line('If you did not request this change or do not recognize the requester, please:')
            ->line('1. Ignore this email')
            ->line('2. Contact support immediately at agrisys0@gmail.com')
            ->line('3. Your email will NOT be changed unless you click the confirmation link above')
            ->line('')
            ->line('For security, we always send email change confirmations to your current email address.')
            ->salutation('Thank you for using AgriSys!');
    }

    /**
     * Email sent to NEW email address as informational notification
     */
    private function notificationEmail(): MailMessage
    {
        return (new MailMessage)
            ->subject('Email Address Change Pending - AgriSys')
            ->greeting('Hello!')
            ->line('Someone has requested to change an AgriSys account email address to this email.')
            ->line('**Important Details:**')
            ->line('• **Old Email:** ' . $this->oldEmail)
            ->line('• **New Email (This address):** ' . $this->newEmail)
            ->line('• **Requested at:** ' . now()->format('F d, Y \\a\\t g:i A'))
            ->line('• **Requested by:** ' . $this->changedBy)
            ->line('')
            ->line('**What happens next?**')
            ->line('The account owner must click the confirmation link sent to the old email address (' . $this->oldEmail . ') before this change takes effect.')
            ->line('')
            ->line('**Did not expect this?**')
            ->line('If you do not recognize this request, you can safely ignore this email. No changes will be made unless confirmed from the old email address.')
            ->line('')
            ->salutation('Thank you for using AgriSys!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'old_email'  => $this->oldEmail,
            'new_email'  => $this->newEmail,
            'changed_by' => $this->changedBy,
            'type'       => $this->type,
        ];
    }
}