<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserRegistration;
use App\Models\PasswordResetOtp;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send OTP to the user's contact number
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string|min:3',
        ], [
            'identifier.required' => 'Please enter your username or contact number.',
            'identifier.min' => 'Please enter a valid username or contact number.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $identifier = trim($request->identifier);

        // Determine if identifier is a contact number or username
        $isContactNumber = preg_match('/^09[0-9]{9}$/', $identifier);

        // Find user by contact number or username
        if ($isContactNumber) {
            $user = UserRegistration::where('contact_number', $identifier)->first();
        } else {
            $user = UserRegistration::where('username', $identifier)->first();
        }

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No account found with this ' . ($isContactNumber ? 'contact number' : 'username') . '.',
            ], 404);
        }

        // Check if user has a contact number
        if (empty($user->contact_number)) {
            return response()->json([
                'success' => false,
                'message' => 'This account has no registered contact number. Please contact support.',
            ], 400);
        }

        $contactNumber = $user->contact_number;

        // Check for rate limiting (max 3 OTPs per hour)
        $recentOtps = PasswordResetOtp::where('contact_number', $contactNumber)
            ->where('created_at', '>', now()->subHour())
            ->count();

        if ($recentOtps >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Too many OTP requests. Please try again after an hour.',
            ], 429);
        }

        // Delete any existing unverified OTPs for this number
        PasswordResetOtp::where('contact_number', $contactNumber)
            ->where('is_verified', false)
            ->delete();

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Create OTP record (expires in 5 minutes)
        $otpRecord = PasswordResetOtp::create([
            'contact_number' => $contactNumber,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
            'is_verified' => false,
            'attempts' => 0,
        ]);

        // Send SMS
        $message = "Your AgriSys password reset OTP is: {$otp}. This code expires in 5 minutes. Do not share this code with anyone.";

        $smsResult = $this->smsService->sendSms($contactNumber, $message);

        if ($smsResult['success']) {
            Log::info('Password reset OTP sent', [
                'contact_number' => $contactNumber,
                'username' => $user->username,
                'otp_id' => $otpRecord->id,
            ]);

            // Mask the contact number for privacy
            $maskedNumber = substr($contactNumber, 0, 4) . '****' . substr($contactNumber, -3);

            return response()->json([
                'success' => true,
                'message' => "OTP sent successfully to {$maskedNumber}.",
                'masked_contact' => $maskedNumber,
                'contact_number' => $contactNumber, // Needed for verification
                'username' => $user->username,
                'expires_in' => 300, // 5 minutes in seconds
            ]);
        } else {
            // Delete the OTP record if SMS failed
            $otpRecord->delete();

            Log::error('Failed to send password reset OTP', [
                'contact_number' => $contactNumber,
                'error' => $smsResult['message'],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again later.',
            ], 500);
        }
    }

    /**
     * Verify the OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_number' => 'required|string|regex:/^09[0-9]{9}$/',
            'otp' => 'required|string|size:6',
        ], [
            'contact_number.required' => 'Contact number is required.',
            'contact_number.regex' => 'Invalid contact number format.',
            'otp.required' => 'Please enter the OTP.',
            'otp.size' => 'OTP must be 6 digits.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $contactNumber = $request->contact_number;
        $otp = $request->otp;

        // Find valid OTP record
        $otpRecord = PasswordResetOtp::where('contact_number', $contactNumber)
            ->where('is_verified', false)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'No OTP found. Please request a new one.',
            ], 404);
        }

        // Check if expired
        if ($otpRecord->isExpired()) {
            $otpRecord->delete();
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired. Please request a new one.',
            ], 410);
        }

        // Check max attempts
        if ($otpRecord->hasMaxAttempts()) {
            $otpRecord->delete();
            return response()->json([
                'success' => false,
                'message' => 'Maximum attempts reached. Please request a new OTP.',
            ], 429);
        }

        // Verify OTP
        if ($otpRecord->otp !== $otp) {
            $otpRecord->incrementAttempts();
            $remainingAttempts = 3 - $otpRecord->attempts;

            return response()->json([
                'success' => false,
                'message' => "Invalid OTP. {$remainingAttempts} attempt(s) remaining.",
            ], 400);
        }

        // Mark OTP as verified
        $otpRecord->markAsVerified();

        Log::info('Password reset OTP verified', [
            'contact_number' => $contactNumber,
            'otp_id' => $otpRecord->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully. You can now reset your password.',
            'reset_token' => base64_encode($contactNumber . '|' . $otpRecord->id . '|' . time()),
        ]);
    }

    /**
     * Reset the password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_number' => 'required|string|regex:/^09[0-9]{9}$/',
            'reset_token' => 'required|string',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#^()_+\-=\[\]{};\':\"\\|,.<>\/])[A-Za-z\d@$!%*?&#^()_+\-=\[\]{};\':\"\\|,.<>\/]{8,}$/',
            ],
        ], [
            'contact_number.required' => 'Contact number is required.',
            'contact_number.regex' => 'Invalid contact number format.',
            'reset_token.required' => 'Reset token is required.',
            'password.required' => 'Please enter a new password.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $contactNumber = $request->contact_number;
        $resetToken = $request->reset_token;

        // Decode and validate token
        $decoded = base64_decode($resetToken);
        $parts = explode('|', $decoded);

        if (count($parts) !== 3) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset token.',
            ], 400);
        }

        [$tokenContact, $otpId, $timestamp] = $parts;

        // Verify token matches contact
        if ($tokenContact !== $contactNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid reset token.',
            ], 400);
        }

        // Check if token is not too old (max 10 minutes)
        if (time() - (int)$timestamp > 600) {
            return response()->json([
                'success' => false,
                'message' => 'Reset session expired. Please start over.',
            ], 410);
        }

        // Verify OTP record exists and is verified
        $otpRecord = PasswordResetOtp::where('id', $otpId)
            ->where('contact_number', $contactNumber)
            ->where('is_verified', true)
            ->first();

        if (!$otpRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired reset session. Please start over.',
            ], 400);
        }

        // Find user
        $user = UserRegistration::where('contact_number', $contactNumber)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Update password (the model has a mutator that hashes it)
        $user->password = $request->password;
        $user->save();

        // Delete the OTP record
        $otpRecord->delete();

        // Clean up old OTPs for this user
        PasswordResetOtp::where('contact_number', $contactNumber)->delete();

        Log::info('Password reset successful', [
            'contact_number' => $contactNumber,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully! You can now login with your new password.',
        ]);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        // Reuse sendOtp logic
        return $this->sendOtp($request);
    }
}
