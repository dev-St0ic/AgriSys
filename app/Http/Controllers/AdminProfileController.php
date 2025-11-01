<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

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

        // Validate request
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // Changed to 5120 KB (5MB)
            'current_password' => ['required_with:password', 'nullable'],
            'password' => [
                'nullable',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
        ]);

        try {
            // Update basic information
            $user->name = $request->name;
            $user->email = $request->email;
            $user->contact_number = $request->contact_number;

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                $file = $request->file('profile_photo');
                
                // Check if file is valid
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
                    return back()->withErrors(['profile_photo' => 'The uploaded file is not valid.'])->withInput();
                }
            }

            // Handle password change
            if ($request->filled('password')) {
                // Verify current password
                if (!Hash::check($request->current_password, $user->password)) {
                    return back()->withErrors(['current_password' => 'The current password is incorrect.'])->withInput();
                }

                // Check if new password is same as current password
                if (Hash::check($request->password, $user->password)) {
                    return back()->withErrors(['password' => 'New password must be different from current password.'])->withInput();
                }

                $user->password = Hash::make($request->password);
            }

            // Save user
            if ($user->save()) {
                return redirect()->route('admin.profile.edit')
                                ->with('success', 'Profile updated successfully!');
            } else {
                throw new \Exception('Failed to save user data');
            }

        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id
            ]);
            
            return back()->withErrors(['error' => 'Failed to update profile. Please try again.'])->withInput();
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