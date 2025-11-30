<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\UserRegistration;

class FacebookDataDeletionController extends Controller
{
    /**
     * Handle data deletion callback from Facebook
     */
    public function handleDataDeletion(Request $request)
    {
        try {
            $signedRequest = $request->input('signed_request');
            
            if (!$signedRequest) {
                return response()->json(['error' => 'Invalid request'], 400);
            }
            
            $data = $this->parseSignedRequest($signedRequest);
            
            if (!$data || !isset($data['user_id'])) {
                return response()->json(['error' => 'Invalid signed request'], 400);
            }
            
            $facebookUserId = $data['user_id'];
            
            // Find and delete user data
            $user = UserRegistration::where('facebook_id', $facebookUserId)->first();
            
            if ($user) {
                Log::info('Facebook data deletion request', [
                    'facebook_id' => $facebookUserId,
                    'user_id' => $user->id
                ]);
                
                // Soft delete user
                $user->delete();
            }
            
            // Generate confirmation code
            $confirmationCode = hash('sha256', $facebookUserId . time());
            
            return response()->json([
                'url' => route('facebook.deletion-status', ['code' => $confirmationCode]),
                'confirmation_code' => $confirmationCode
            ]);
            
        } catch (\Exception $e) {
            Log::error('Facebook data deletion error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
    
    /**
     * Show deletion status page
     */
    public function showDeletionStatus(Request $request)
    {
        return view('facebook.deletion-status', [
            'confirmation_code' => $request->query('code')
        ]);
    }
    
    /**
     * Parse Facebook signed request
     */
    private function parseSignedRequest($signedRequest)
    {
        list($encodedSig, $payload) = explode('.', $signedRequest, 2);
        
        $secret = config('services.facebook.client_secret');
        
        $sig = $this->base64UrlDecode($encodedSig);
        $data = json_decode($this->base64UrlDecode($payload), true);
        
        if (!isset($data['algorithm']) || strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
            return null;
        }
        
        $expectedSig = hash_hmac('sha256', $payload, $secret, true);
        
        if ($sig !== $expectedSig) {
            return null;
        }
        
        return $data;
    }
    
    private function base64UrlDecode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
}