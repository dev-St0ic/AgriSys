<?php

namespace App\Http\Controllers;

use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\UserRegistration;

class SmsNotificationController extends Controller
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send custom notification to user
     */
    public function sendNotification(Request $request): JsonResponse
    {
        // Only allow admin users to send notifications
        if (!auth()->check() || !auth()->user()->hasAdminPrivileges()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string|min:10|max:15',
            'title' => 'required|string|max:50',
            'message' => 'required|string|max:300'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->smsService->sendNotification(
                $request->phone_number,
                $request->title,
                $request->message
            );

            if ($result['success']) {
                Log::info('Admin notification sent', [
                    'phone' => $request->phone_number,
                    'title' => $request->title,
                    'admin_id' => auth()->id()
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Notification sent successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Admin notification failed', [
                'phone' => $request->phone_number,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification'
            ], 500);
        }
    }

    /**
     * Get SMS service status
     */
    public function getServiceStatus(): JsonResponse
    {
        // Only allow admin users to check service status
        if (!auth()->check() || !auth()->user()->hasAdminPrivileges()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            $status = $this->smsService->getServiceStatus();
            $balance = $this->smsService->checkBalance();

            return response()->json([
                'success' => true,
                'service_status' => $status,
                'balance_info' => $balance,
                'available' => $this->smsService->isAvailable()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get service status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}