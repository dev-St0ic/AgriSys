<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use App\Notifications\PasswordChangeNotification;
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

        // Check if email is being changed
        $emailChanged = $request->email !== $user->email;

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
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:10240'], // 10MB
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
            // Start database transaction for data integrity
            DB::beginTransaction();

            // Update basic information
            $user->name = $request->name;
            $user->email = $request->email;
            $user->contact_number = $request->contact_number;

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $file = $request->file('profile_photo');
                
                if ($file->isValid()) {
                    // Delete old profile photo if exists
                    if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
                        Storage::disk('public')->delete($user->profile_photo);
                    }

                    // Store new profile photo
                    $path = $file->store('profile-photos', 'public');
                    
                    if ($path) {
                        $user->profile_photo = $path;
                        Log::info('Profile photo uploaded successfully', ['path' => $path]);
                    } else {
                        throw new \Exception('Failed to store profile photo');
                    }
                } else {
                    DB::rollBack();
                    return back()->withErrors(['profile_photo' => 'The uploaded file is not valid.'])->withInput();
                }
            }

            // Handle email change - mark as unverified
            if ($emailChanged) {
                $user->email_verified_at = null;
                
                Log::info('Email changed - verification required', [
                    'user_id' => $user->id,
                    'old_email' => $user->getOriginal('email'),
                    'new_email' => $request->email
                ]);
            }

            // Handle password change with email verification
            if ($request->filled('password')) {
                // Verify current password
                if (!Hash::check($request->current_password, $user->password)) {
                    DB::rollBack();
                    return back()->withErrors(['current_password' => 'The current password is incorrect.'])->withInput();
                }

                // Check if new password is same as current password
                if (Hash::check($request->password, $user->password)) {
                    DB::rollBack();
                    return back()->withErrors(['password' => 'New password must be different from current password.'])->withInput();
                }

                // Generate a unique token
                $token = Str::random(60);
                
                // Clear any existing password change tokens for this user
                DB::table('password_change_tokens')
                    ->where('user_id', $user->id)
                    ->delete();
                
                // Store the password change request
                DB::table('password_change_tokens')->insert([
                    'user_id' => $user->id,
                    'token' => hash('sha256', $token),
                    'new_password' => Hash::make($request->password),
                    'created_at' => now()
                ]);

                // Save other profile changes first
                if (!$user->save()) {
                    DB::rollBack();
                    throw new \Exception('Failed to save user data');
                }

                // Commit the transaction before sending email
                DB::commit();

                // Send verification email for email change if email was changed
                if ($emailChanged) {
                    try {
                        $user->sendEmailVerificationNotification();
                        Log::info('Email verification sent after email change', [
                            'user_id' => $user->id,
                            'new_email' => $user->email
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to send email verification', [
                            'user_id' => $user->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                // Send verification email for password change
                try {
                    $user->notify(new PasswordChangeNotification($token, $request->password));
                    
                    Log::info('Password change verification email sent', [
                        'user_id' => $user->id,
                        'email' => $user->email
                    ]);

                    $message = 'Profile updated! A verification email has been sent to ' . $user->email . '. Please check your inbox to confirm your password change.';
                    
                    if ($emailChanged) {
                        $message .= ' You will also need to verify your new email address.';
                    }

                    return redirect()->route('admin.profile.edit')
                        ->with('info', $message);
                        
                } catch (\Exception $e) {
                    Log::error('Failed to send password change verification email', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage()
                    ]);
                    
                    return redirect()->route('admin.profile.edit')
                        ->with('warning', 'Profile updated, but we couldn\'t send the verification email. Please try changing your password again.');
                }
            } else {
                // Save user without password change
                if ($user->save()) {
                    DB::commit();
                    
                    // Send email verification if email was changed
                    if ($emailChanged) {
                        try {
                            $user->sendEmailVerificationNotification();
                            
                            Log::info('Email verification sent', [
                                'user_id' => $user->id,
                                'new_email' => $user->email
                            ]);
                            
                            return redirect()->route('admin.profile.edit')
                                ->with('success', 'Profile updated successfully! A verification email has been sent to ' . $user->email . '. Please verify your new email address.');
                        } catch (\Exception $e) {
                            Log::error('Failed to send email verification', [
                                'user_id' => $user->id,
                                'error' => $e->getMessage()
                            ]);
                            
                            return redirect()->route('admin.profile.edit')
                                ->with('warning', 'Profile updated, but we couldn\'t send the verification email. Please try again.');
                        }
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
            
            return back()->withErrors(['error' => 'Failed to update profile. Please try again. Error: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Verify password change from email link
     */
    public function verifyPasswordChange(Request $request, $user)
    {
        try {
            // Find the user
            $userModel = User::findOrFail($user);
            
            // Get the token from the request
            $token = $request->token;
            
            if (!$token) {
                Log::warning('Password change verification attempted without token', [
                    'user_id' => $user,
                    'ip' => $request->ip()
                ]);
                
                return redirect()->route('login')
                    ->with('error', 'Invalid verification link. No token provided.');
            }

            // Find the password change request
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

            // Check if token is expired (60 minutes)
            if (now()->diffInMinutes($passwordChangeToken->created_at) > 60) {
                // Clean up expired token
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

            // Start transaction for password update
            DB::beginTransaction();

            try {
                // Update the password
                $userModel->password = $passwordChangeToken->new_password;
                $userModel->save();

                // Delete the token
                DB::table('password_change_tokens')
                    ->where('user_id', $userModel->id)
                    ->delete();

                // SECURITY: Invalidate all existing sessions for this user
                // This logs out the user from all devices
                DB::table('sessions')
                    ->where('user_id', $userModel->id)
                    ->delete();

                // If the current user is logged in and is the same user, log them out
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