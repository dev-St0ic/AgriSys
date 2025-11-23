<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $apiKey;
    protected $senderId;
    protected $baseUrl;
    protected $timeout;
    protected $enabled;

    public function __construct()
    {
        $this->apiKey = config('services.philsms.api_key');
        $this->senderId = config('services.philsms.sender_id');
        $this->baseUrl = config('services.philsms.base_url');
        $this->timeout = config('services.philsms.timeout');
        $this->enabled = config('services.philsms.enabled');
    }

    /**
     * Send SMS message
     *
     * @param string $phoneNumber
     * @param string $message
     * @return array
     */
    public function sendSms($phoneNumber, $message)
    {
        if (!$this->enabled) {
            Log::info('SMS sending is disabled. Message would be sent to: ' . $phoneNumber);
            return ['success' => false, 'message' => 'SMS service is disabled'];
        }

        if (!$this->apiKey) {
            Log::error('PhilSMS API key not configured');
            return ['success' => false, 'message' => 'SMS service not configured'];
        }

        // Format phone number (ensure it starts with +63)
        $formattedNumber = $this->formatPhoneNumber($phoneNumber);

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])
                ->post($this->baseUrl . '/sms/send', [
                    'recipient' => $formattedNumber,
                    'sender_id' => $this->senderId,
                    'type' => 'plain',
                    'message' => $message,
                ]);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['data'])) {
                Log::info('SMS sent successfully', [
                    'phone' => $formattedNumber,
                    'message_id' => $responseData['data']['id'] ?? null
                ]);

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'message_id' => $responseData['data']['id'] ?? null,
                    'response' => $responseData
                ];
            } else {
                Log::warning('SMS sending failed', [
                    'phone' => $formattedNumber,
                    'response' => $responseData,
                    'status_code' => $response->status()
                ]);

                return [
                    'success' => false,
                    'message' => $responseData['message'] ?? 'Failed to send SMS',
                    'response' => $responseData
                ];
            }
        } catch (\Exception $e) {
            Log::error('SMS sending exception', [
                'phone' => $formattedNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'SMS service error: ' . $e->getMessage()
            ];
        }
    }



    /**
     * Send account verification notification
     *
     * @param string $phoneNumber
     * @param string $fullName
     * @param string $status (approved/rejected)
     * @param string|null $reason
     * @return array
     */
    public function sendAccountVerificationNotification($phoneNumber, $fullName, $status, $reason = null)
    {
        if ($status === 'approved') {
            $message = "Congratulations {$fullName}! Your AgriSys account has been APPROVED. You can now access all features. Welcome to AgriSys!";
        } elseif ($status === 'rejected') {
            $reasonText = $reason ? " Reason: {$reason}" : "";
            $message = "Hello {$fullName}, your AgriSys account verification was not approved.{$reasonText} Please contact support for assistance.";
        } else {
            $message = "Hello {$fullName}, your AgriSys account status has been updated to: " . ucfirst($status);
        }

        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Send application approval/rejection notification
     *
     * @param string $phoneNumber
     * @param string $fullName
     * @param string $applicationType (e.g., 'RSBSA', 'Seedling Request', 'FishR', 'BoatR', 'Training')
     * @param string $status (approved/rejected)
     * @param string|null $reason
     * @return array
     */
    public function sendApplicationStatusNotification($phoneNumber, $fullName, $applicationType, $status, $reason = null)
    {
        if ($status === 'approved') {
            $message = "Good news {$fullName}! Your {$applicationType} application has been APPROVED. You can now proceed with the next steps. - AgriSys";
        } elseif ($status === 'rejected') {
            $reasonText = $reason ? " Reason: {$reason}" : "";
            $message = "Hello {$fullName}, your {$applicationType} application was not approved.{$reasonText} Please contact our office for assistance. - AgriSys";
        } else {
            $message = "Hello {$fullName}, your {$applicationType} application status has been updated to: " . ucfirst($status) . " - AgriSys";
        }

        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Send generic notification
     *
     * @param string $phoneNumber
     * @param string $title
     * @param string $content
     * @return array
     */
    public function sendNotification($phoneNumber, $title, $content)
    {
        $message = "{$title}: {$content} - AgriSys";
        return $this->sendSms($phoneNumber, $message);
    }

    /**
     * Check SMS balance (if supported by PhilSMS)
     *
     * @return array
     */
    public function checkBalance()
    {
        if (!$this->enabled || !$this->apiKey) {
            return ['success' => false, 'message' => 'SMS service not configured'];
        }

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json'
                ])
                ->get($this->baseUrl . '/balance');

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['data'])) {
                return [
                    'success' => true,
                    'balance' => $responseData['data']['balance'] ?? 'Unknown',
                    'response' => $responseData
                ];
            } else {
                return [
                    'success' => false,
                    'balance' => 'Unknown',
                    'message' => $responseData['message'] ?? 'Failed to check balance',
                    'response' => $responseData
                ];
            }
        } catch (\Exception $e) {
            Log::error('SMS balance check exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Failed to check balance'];
        }
    }

    /**
     * Format phone number to PhilSMS format
     * Only accepts 09XXXXXXXXX format and converts to 639XXXXXXXXX
     *
     * @param string $phoneNumber
     * @return string
     */
    protected function formatPhoneNumber($phoneNumber)
    {
        // Remove all non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $phoneNumber);

        // Only accept 09XXXXXXXXX format (11 digits starting with 09)
        if (strlen($number) === 11 && substr($number, 0, 2) === '09') {
            // Convert 09XXXXXXXXX to 639XXXXXXXXX for PhilSMS API
            return '63' . substr($number, 1);
        }

        // If it doesn't match expected pattern, return as-is and let API handle error
        return $number;
    }



    /**
     * Check if SMS service is available
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->enabled && !empty($this->apiKey);
    }

    /**
     * Get service configuration status
     *
     * @return array
     */
    public function getServiceStatus()
    {
        return [
            'enabled' => $this->enabled,
            'configured' => !empty($this->apiKey),
            'sender_id' => $this->senderId,
            'base_url' => $this->baseUrl,
        ];
    }
}
