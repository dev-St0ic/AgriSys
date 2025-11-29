<?php

namespace App\Listeners;

use App\Events\AccountVerificationStatusChanged;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendAccountVerificationSms // Removed ShouldQueue for immediate processing
{
    // Removed InteractsWithQueue trait

    protected SmsService $smsService;

    /**
     * Create the event listener.
     */
    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Handle the event.
     */
    public function handle(AccountVerificationStatusChanged $event): void
    {
        $userRegistration = $event->userRegistration;
        $newStatus = $event->newStatus;
        $reason = $event->reason;

        Log::info('SendAccountVerificationSms listener triggered', [
            'user_id' => $userRegistration->id,
            'email' => $userRegistration->email,
            'new_status' => $newStatus,
            'phone' => $userRegistration->contact_number,
            'timestamp' => now()->toDateTimeString()
        ]);

        // Only send SMS for approved or rejected status
        if (!in_array($newStatus, ['approved', 'rejected'])) {
            Log::info('SMS not sent - status not approved/rejected', ['status' => $newStatus]);
            return;
        }

        // Check if user has a contact number
        if (empty($userRegistration->contact_number)) {
            Log::warning('Cannot send SMS verification notification: No contact number', [
                'user_id' => $userRegistration->id,
                'email' => $userRegistration->email,
                'status' => $newStatus
            ]);
            return;
        }

        try {
            // Send SMS notification
            $result = $this->smsService->sendAccountVerificationNotification(
                $userRegistration->contact_number,
                $userRegistration->full_name,
                $newStatus,
                $reason
            );

            if ($result['success']) {
                Log::info('Account verification SMS sent successfully', [
                    'user_id' => $userRegistration->id,
                    'email' => $userRegistration->email,
                    'phone' => $userRegistration->contact_number,
                    'status' => $newStatus,
                    'message_id' => $result['message_id'] ?? null
                ]);
            } else {
                Log::error('Failed to send account verification SMS', [
                    'user_id' => $userRegistration->id,
                    'email' => $userRegistration->email,
                    'phone' => $userRegistration->contact_number,
                    'status' => $newStatus,
                    'error' => $result['message']
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception occurred while sending account verification SMS', [
                'user_id' => $userRegistration->id,
                'email' => $userRegistration->email,
                'phone' => $userRegistration->contact_number,
                'status' => $newStatus,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
