<?php

namespace App\Listeners;

use App\Events\ApplicationStatusChanged;
use App\Services\SmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendApplicationStatusSms implements ShouldQueue
{
    use InteractsWithQueue;

    protected SmsService $smsService;

    // Track processed events to prevent duplicates
    private static $processedEvents = [];

    // Set queue connection and queue name
    public $connection = 'sync'; // Use 'sync' for immediate processing without actual queue
    public $queue = 'notifications';

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
    public function handle(ApplicationStatusChanged $event): void
    {
        $application = $event->application;
        $applicationType = $event->applicationType;
        $newStatus = $event->newStatus;
        $reason = $event->reason;
        $applicantPhone = $event->applicantPhone;
        $applicantName = $event->applicantName;

        // Create unique event identifier
        $eventId = md5(
            ($application->id ?? 'unknown') .
            $applicationType .
            $newStatus .
            $applicantPhone .
            now()->format('Y-m-d H:i:s')
        );

        // Check if this exact event was already processed in the last 5 seconds
        $currentTime = time();
        self::$processedEvents = array_filter(self::$processedEvents, function($timestamp) use ($currentTime) {
            return ($currentTime - $timestamp) < 5; // Keep events from last 5 seconds
        });

        if (isset(self::$processedEvents[$eventId])) {
            Log::warning('Duplicate SMS event detected and prevented', [
                'event_id' => $eventId,
                'application_id' => $application->id ?? 'unknown',
                'application_type' => $applicationType,
                'new_status' => $newStatus,
                'phone' => $applicantPhone
            ]);
            return;
        }

        // Mark this event as processed
        self::$processedEvents[$eventId] = $currentTime;

        Log::info('SendApplicationStatusSms listener triggered', [
            'event_id' => $eventId,
            'application_id' => $application->id ?? 'unknown',
            'application_type' => $applicationType,
            'new_status' => $newStatus,
            'phone' => $applicantPhone,
            'timestamp' => now()->toDateTimeString()
        ]);

        // Only send SMS for approved or rejected status
        if (!in_array($newStatus, ['approved', 'rejected'])) {
            Log::info('SMS not sent - status not approved/rejected', ['status' => $newStatus]);
            return;
        }

        // Check if we have phone number and name
        if (empty($applicantPhone)) {
            Log::warning('Cannot send SMS application notification: No phone number', [
                'application_id' => $application->id ?? 'unknown',
                'application_type' => $applicationType,
                'status' => $newStatus
            ]);
            return;
        }

        if (empty($applicantName)) {
            $applicantName = 'Applicant'; // Fallback name
        }

        try {
            // Send SMS notification
            $result = $this->smsService->sendApplicationStatusNotification(
                $applicantPhone,
                $applicantName,
                $applicationType,
                $newStatus,
                $reason
            );

            if ($result['success']) {
                Log::info('Application status SMS sent successfully', [
                    'application_id' => $application->id ?? 'unknown',
                    'application_type' => $applicationType,
                    'phone' => $applicantPhone,
                    'status' => $newStatus,
                    'message_id' => $result['message_id'] ?? null
                ]);
            } else {
                Log::error('Failed to send application status SMS', [
                    'application_id' => $application->id ?? 'unknown',
                    'application_type' => $applicationType,
                    'phone' => $applicantPhone,
                    'status' => $newStatus,
                    'error' => $result['message']
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Exception occurred while sending application status SMS', [
                'application_id' => $application->id ?? 'unknown',
                'application_type' => $applicationType,
                'phone' => $applicantPhone,
                'status' => $newStatus,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
