<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $apiKey;
    protected $senderName;
    protected $baseUrl;
    protected $timeout;
    protected $enabled;

    public function __construct()
    {
        $this->apiKey = config('services.semaphore.api_key');
        $this->senderName = config('services.semaphore.sender_name');
        $this->baseUrl = config('services.semaphore.base_url');
        $this->timeout = config('services.semaphore.timeout');
        $this->enabled = config('services.semaphore.enabled');
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
            Log::error('Semaphore API key not configured');
            return ['success' => false, 'message' => 'SMS service not configured'];
        }

        // Format phone number
        $formattedNumber = $this->formatPhoneNumber($phoneNumber);

        try {
            // Semaphore API uses form parameters
            $params = [
                'apikey' => $this->apiKey,
                'number' => $formattedNumber,
                'message' => $message,
            ];
            
            // Only add sendername if it's set and not empty
            if (!empty($this->senderName)) {
                $params['sendername'] = $this->senderName;
            }
            
            $response = Http::timeout($this->timeout)
                ->asForm()
                ->post($this->baseUrl . '/messages', $params);

            $responseData = $response->json();

            // Semaphore returns array of message objects on success
            if ($response->successful() && isset($responseData[0]['message_id'])) {
                Log::info('SMS sent successfully via Semaphore', [
                    'phone' => $formattedNumber,
                    'message_id' => $responseData[0]['message_id'] ?? null
                ]);

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'message_id' => $responseData[0]['message_id'] ?? null,
                    'response' => $responseData
                ];
            } else {
                Log::warning('SMS sending failed via Semaphore', [
                    'phone' => $formattedNumber,
                    'response' => $responseData,
                    'status_code' => $response->status()
                ]);

                return [
                    'success' => false,
                    'message' => $responseData['message'] ?? $responseData['error'] ?? 'Failed to send SMS',
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
            // Special message for Supplies/Seedling requests with pickup information
            if (stripos($applicationType, 'Seedling') !== false || stripos($applicationType, 'Supplies') !== false) {
                $message = "Good news {$fullName}! Your {$applicationType} application has been APPROVED. You have 30 DAYS to pick up your items from our office (Mon-Fri, 8AM-5PM). Bring a valid ID. - AgriSys";
            } else {
                $message = "Good news {$fullName}! Your {$applicationType} application has been APPROVED. You can now proceed with the next steps. - AgriSys";
            }
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
     * Check SMS balance via Semaphore
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
                ->get($this->baseUrl . '/account', [
                    'apikey' => $this->apiKey
                ]);

            $responseData = $response->json();

            if ($response->successful() && isset($responseData['credit_balance'])) {
                return [
                    'success' => true,
                    'balance' => $responseData['credit_balance'],
                    'account_name' => $responseData['account_name'] ?? 'Unknown',
                    'response' => $responseData
                ];
            } else {
                return [
                    'success' => false,
                    'balance' => 'Unknown',
                    'message' => $responseData['message'] ?? $responseData['error'] ?? 'Failed to check balance',
                    'response' => $responseData
                ];
            }
        } catch (\Exception $e) {
            Log::error('SMS balance check exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Failed to check balance'];
        }
    }

    /**
     * Format phone number for Semaphore API
     * Accepts 09XXXXXXXXX format and converts to 639XXXXXXXXX
     *
     * @param string $phoneNumber
     * @return string
     */
    protected function formatPhoneNumber($phoneNumber)
    {
        // Remove all non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $phoneNumber);

        // If starts with 09, convert to 639 format
        if (strlen($number) === 11 && substr($number, 0, 2) === '09') {
            return '63' . substr($number, 1);
        }

        // If already in 639 format, return as-is
        if (strlen($number) === 12 && substr($number, 0, 2) === '63') {
            return $number;
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
            'sender_name' => $this->senderName,
            'base_url' => $this->baseUrl,
        ];
    }
}
