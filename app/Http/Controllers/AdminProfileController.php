<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use App\Notifications\PasswordChangeNotification;
use App\Notifications\EmailChangeNotification;
use App\Models\User;

class AdminProfileController extends Controller
{
    /**
     * Show the profile edit form
     */
    public function edit()
    {
        return view('admin.profile.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Update the admin profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Store old email BEFORE validation
        $oldEmail = $user->email;
        $emailChanged = $request->email !== $oldEmail;

        // Require current password for email changes
        if ($emailChanged) {
            $request->validate([
                'email_change_password' => 'required|string'
            ], [
                'email_change_password.required' => 'Please enter your current password to change the email address.'
            ]);
            
            if (!Hash::check($request->email_change_password, $user->password)) {
                return redirect()->back()
                    ->withErrors(['email_change_password' => 'Current password is incorrect.'])
                    ->withInput();
            }
        }

        // Validate request
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10240'],
            'current_password' => ['required_with:password', 'nullable'],
            'password' => [
                'nullable',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ]);

        try {
            DB::beginTransaction();

            // Update basic information (name, contact, photo) - NOT email yet
            $user->name = $request->name;
            $user->contact_number = $request->contact_number;

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $file = $request->file('profile_photo');
                
                if ($file->isValid()) {
                    if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                        Storage::disk('public')->delete($user->profile_photo);
                    }

                    $path = $file->store('profile-photos', 'public');
                    
                    if ($path) {
                        $user->profile_photo = $path;
                        Log::info('Profile photo uploaded successfully', ['path' => $path]);
                    } else {
                        DB::rollBack();
                        return back()->withErrors(['profile_photo' => 'Failed to upload photo. Please try again.'])->withInput();
                    }
                } else {
                    DB::rollBack();
                    return back()->withErrors(['profile_photo' => 'The uploaded file is not valid.'])->withInput();
                }
            }

            // Handle EMAIL CHANGE - TWO-STEP FLOW WITH TWO NOTIFICATIONS
            if ($emailChanged) {
                // Generate secure token
                $token = Str::random(60);
                
                // Delete any existing email change tokens for this user
                DB::table('email_change_tokens')
                    ->where('user_id', $user->id)
                    ->delete();
                
                // Store the email change request
                DB::table('email_change_tokens')->insert([
                    'user_id' => $user->id,
                    'old_email' => $oldEmail,
                    'new_email' => $request->email,
                    'token' => hash('sha256', $token),
                    'created_at' => now()
                ]);

                Log::info('Email change initiated - preparing notifications', [
                    'user_id' => $user->id,
                    'old_email' => $oldEmail,
                    'new_email' => $request->email
                ]);

                // SEND 1: Confirmation email to OLD email address (with link to confirm)
                try {
                    Notification::route('mail', $oldEmail)
                        ->notify(new EmailChangeNotification(
                            $user,  
                            $oldEmail,
                            $request->email,
                            'yourself',
                            $token,
                            'confirmation' // Type: confirmation
                        ));
                    
                    Log::info('Email change confirmation sent to old email', [
                        'user_id' => $user->id,
                        'old_email' => $oldEmail,
                        'new_email' => $request->email
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Failed to send email change confirmation email', [
                        'user_id' => $user->id,
                        'old_email' => $oldEmail,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    return back()->withErrors([
                        'email' => 'Failed to send confirmation email to ' . $oldEmail . '. Please try again.'
                    ])->withInput();
                }

                // SEND 2: Informational email to NEW email address (notifying about the change)
                try {
                    Notification::route('mail', $request->email)
                        ->notify(new EmailChangeNotification(
                            $user,  
                            $oldEmail,
                            $request->email,
                            'yourself',
                            null,
                            'notification' // Type: notification
                        ));
                    
                    Log::info('Email change notification sent to new email', [
                        'user_id' => $user->id,
                        'old_email' => $oldEmail,
                        'new_email' => $request->email
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to send email change notification to new email', [
                        'user_id' => $user->id,
                        'new_email' => $request->email,
                        'error' => $e->getMessage()
                    ]);
                    // Don't fail the transaction for this - it's informational only
                }
                
                // IMPORTANT: DO NOT update email yet - keep old email
                // Email will be updated after confirmation
                
                Log::info('Email change awaiting confirmation', [
                    'user_id' => $user->id,
                    'old_email' => $oldEmail,
                    'new_email' => $request->email,
                    'status' => 'pending_confirmation'
                ]);
            }

            // Handle password change
            if ($request->filled('password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    DB::rollBack();
                    return back()->withErrors(['current_password' => 'The current password is incorrect.'])->withInput();
                }

                if (Hash::check($request->password, $user->password)) {
                    DB::rollBack();
                    return back()->withErrors(['password' => 'New password must be different from current password.'])->withInput();
                }

                $token = Str::random(60);
                
                DB::table('password_change_tokens')
                    ->where('user_id', $user->id)
                    ->delete();
                
                DB::table('password_change_tokens')->insert([
                    'user_id' => $user->id,
                    'token' => hash('sha256', $token),
                    'new_password' => Hash::make($request->password),
                    'created_at' => now()
                ]);

                if (!$user->save()) {
                    DB::rollBack();
                    throw new \Exception('Failed to save user data');
                }

                DB::commit();

                // Send password change verification
                try {
                    $user->notify(new PasswordChangeNotification($token, $request->password));
                    
                    Log::info('Password change verification email sent', [
                        'user_id' => $user->id,
                        'email' => $user->email
                    ]);

                    $message = 'Profile updated! A verification email has been sent to ' . $user->email . ' to confirm your password change.';
                    
                    if ($emailChanged) {
                        $message .= ' Additionally, a confirmation link has been sent to your old email address (' . $oldEmail . ') to confirm the email change, and a notification has been sent to your new email address (' . $request->email . ').';
                    }

                    return redirect()->route('admin.profile.edit')
                        ->with('success', $message);
                        
                } catch (\Exception $e) {
                    Log::error('Failed to send password change verification email', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                    
                    return redirect()->route('admin.profile.edit')
                        ->with('warning', 'Profile updated, but we couldn\'t send the verification email. Please try changing your password again.');
                }
            } else {
                // Save without password change
                if ($user->save()) {
                    DB::commit();
                    
                    if ($emailChanged) {
                        return redirect()->route('admin.profile.edit')
                            ->with('success', 'Profile updated! A confirmation link has been sent to your old email address (' . $oldEmail . '). Please check your inbox and click the link to confirm the email change. A notification has also been sent to your new email address (' . $request->email . ').');
                    }
                    
                    return redirect()->route('admin.profile.edit')
                        ->with('success', 'Profile updated successfully!');
                } else {
                    DB::rollBack();
                    throw new \Exception('Failed to save user data');
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Profile update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user->id
            ]);
            
            return back()->withErrors(['error' => 'Failed to update profile. Please try again.'])->withInput();
        }
    }

    /**
     * Verify password change from email link
     */
    public function verifyPasswordChange(Request $request, $user)
    {
        try {
            $userModel = User::findOrFail($user);
            $token = $request->token;
            
            if (!$token) {
                Log::warning('Password change verification attempted without token', [
                    'user_id' => $user,
                    'ip' => $request->ip()
                ]);
                
                return redirect()->route('login')
                    ->with('error', 'Invalid verification link. No token provided.');
            }

            $passwordChangeToken = DB::table('password_change_tokens')
                ->where('user_id', $userModel->id)
                ->where('token', hash('sha256', $token))
                ->first();

            if (!$passwordChangeToken) {
                Log::warning('Password change verification failed - token not found', [
                    'user_id' => $userModel->id,
                    'ip' => $request->ip()
                ]);
                
                return redirect()->route('login')
                    ->with('error', 'Invalid or expired verification link. The token may have already been used or has expired.');
            }

            if (now()->diffInMinutes($passwordChangeToken->created_at) > 60) {
                DB::table('password_change_tokens')
                    ->where('user_id', $userModel->id)
                    ->delete();
                
                Log::info('Password change token expired', [
                    'user_id' => $userModel->id,
                    'created_at' => $passwordChangeToken->created_at
                ]);
                    
                return redirect()->route('login')
                    ->with('error', 'Verification link has expired (valid for 60 minutes). Please request a new password change.');
            }

            DB::beginTransaction();

            try {
                $userModel->password = $passwordChangeToken->new_password;
                $userModel->save();

                DB::table('password_change_tokens')
                    ->where('user_id', $userModel->id)
                    ->delete();

                DB::table('sessions')
                    ->where('user_id', $userModel->id)
                    ->delete();

                if (Auth::check() && Auth::id() === $userModel->id) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }

                DB::commit();

                Log::info('Password changed successfully via email verification', [
                    'user_id' => $userModel->id,
                    'user_email' => $userModel->email,
                    'ip' => $request->ip(),
                    'sessions_invalidated' => true
                ]);

                return redirect()->route('login')
                    ->with('success', 'Password changed successfully! For security, you have been logged out from all devices. Please login with your new password.');
                    
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Password change verification - user not found', [
                'user_id' => $user,
                'ip' => $request->ip()
            ]);
            
            return redirect()->route('login')
                ->with('error', 'User account not found.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Password change verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user,
                'ip' => $request->ip()
            ]);
            
            return redirect()->route('login')
                ->with('error', 'Failed to verify password change. Please try again or contact support.');
        }
    }

    /**
    * Confirm email change from link in old email
    */
    public function confirmEmailChange(Request $request, $user)
    {
        try {
            $userModel = User::findOrFail($user);
            $token = $request->token;
            
            if (!$token) {
                Log::warning('Email change confirmation attempted without token', [
                    'user_id' => $user,
                    'ip' => $request->ip()
                ]);
                
                return redirect()->route('login')
                    ->with('error', 'Invalid confirmation link. No token provided.');
            }

            $emailChangeToken = DB::table('email_change_tokens')
                ->where('user_id', $userModel->id)
                ->where('token', hash('sha256', $token))
                ->first();

            if (!$emailChangeToken) {
                Log::warning('Email change confirmation failed - token not found', [
                    'user_id' => $userModel->id,
                    'ip' => $request->ip()
                ]);
                
                return redirect()->route('login')
                    ->with('error', 'Invalid or expired confirmation link. The token may have already been used or has expired.');
            }

            // Check if token is expired (24 hours)
            if (now()->diffInHours($emailChangeToken->created_at) > 24) {
                DB::table('email_change_tokens')
                    ->where('user_id', $userModel->id)
                    ->delete();
                
                Log::info('Email change token expired', [
                    'user_id' => $userModel->id,
                    'created_at' => $emailChangeToken->created_at
                ]);
                    
                return redirect()->route('login')
                    ->with('error', 'Confirmation link has expired (valid for 24 hours). Please request a new email change.');
            }

            DB::beginTransaction();

            try {
                // Update email address
                $oldEmail = $userModel->email;
                $newEmail = $emailChangeToken->new_email;
                
                $userModel->email = $newEmail;
                $userModel->email_verified_at = null; // Require verification of new email
                $userModel->save();

                // Delete the used token
                DB::table('email_change_tokens')
                    ->where('user_id', $userModel->id)
                    ->delete();

                DB::commit();

                // Send verification email to NEW email address
                try {
                    $userModel->sendEmailVerificationNotification();
                    
                    Log::info('Email changed successfully and verification sent', [
                        'user_id' => $userModel->id,
                        'old_email' => $oldEmail,
                        'new_email' => $newEmail,
                        'ip' => $request->ip()
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send verification to new email', [
                        'user_id' => $userModel->id,
                        'error' => $e->getMessage()
                    ]);
                }

                return redirect()->route('login')
                    ->with('success', 'Email address changed successfully! A verification email has been sent to ' . $newEmail . '. Please verify your new email address before logging in.');
                    
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Email change confirmation - user not found', [
                'user_id' => $user,
                'ip' => $request->ip()
            ]);
            
            return redirect()->route('login')
                ->with('error', 'User account not found.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Email change confirmation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => $user,
                'ip' => $request->ip()
            ]);
            
            return redirect()->route('login')
                ->with('error', 'Failed to confirm email change. Please try again or contact support.');
        }
    }

    /**
     * Delete profile photo
     */
    public function deletePhoto()
    {
        try {
            $user = Auth::user();

            if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                Storage::disk('public')->delete($user->profile_photo);
                $user->profile_photo = null;
                $user->save();
                
                Log::info('Profile photo deleted', ['user_id' => $user->id]);
                
                return redirect()->route('admin.profile.edit')
                    ->with('success', 'Profile photo deleted successfully!');
            }

            return redirect()->route('admin.profile.edit')
                ->with('error', 'No profile photo to delete.');

        } catch (\Exception $e) {
            Log::error('Photo deletion failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            
            return back()->withErrors(['error' => 'Failed to delete photo. Please try again.']);
        }
    }
}