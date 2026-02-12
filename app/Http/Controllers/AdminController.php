<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Services\RecycleBinService;
use App\Notifications\AdminPasswordResetByAdminNotification;
use App\Notifications\EmailChangeNotification;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Display a listing of the admins.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $admins = User::whereIn('role', ['admin', 'superadmin'])->paginate(10);
        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.admins.create');
    }

    /**
     * Store a newly created admin in storage.
     */
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->role === 'superadmin' && User::superadminExists()) {
            return redirect()->back()
                ->withErrors(['role' => 'A SuperAdmin already exists. Only Admins can be created.'])
                ->withInput();
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,superadmin',
        ]);

        $admin = User::create([  
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $admin->sendEmailVerificationNotification();

        $this->logActivity('created', 'User', null, [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role
        ]);

        return redirect()->route('admin.admins.index')
                        ->with('success', 'Admin created successfully.');
    }

    /**
     * Display the specified admin.
     */
    public function show(User $admin)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.admins.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit(User $admin)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.admins.edit', compact('admin'));
    }

    /**
     * Update the specified admin in storage.
     */
    public function update(Request $request, User $admin)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        // Check superadmin constraints
        if ($admin->isSuperAdmin() && $request->role !== 'superadmin') {
            $otherSuperAdmins = User::where('id', '!=', $admin->id)
                ->where('role', 'superadmin')
                ->where('deleted_at', null)
                ->count();

            if ($otherSuperAdmins === 0) {
                return redirect()->back()
                    ->withErrors(['role' => 'Cannot downgrade the only SuperAdmin.'])
                    ->withInput();
            }
        }

        if ($request->role === 'superadmin' && !$admin->isSuperAdmin()) {
            if (User::superadminExists()) {
                return redirect()->back()
                    ->withErrors(['role' => 'A SuperAdmin already exists. Cannot create another.'])
                    ->withInput();
            }
        }

        // Store old email BEFORE any changes
        $oldEmail = $admin->email;
        $emailChanged = $request->email !== $oldEmail;

        // Require current password for email changes
        if ($emailChanged) {
            $request->validate([
                'current_password' => 'required|string'
            ], [
                'current_password.required' => 'Please enter the current password to change the email address.'
            ]);
            
            if (!Hash::check($request->current_password, $admin->password)) {
                return redirect()->back()
                    ->withErrors(['current_password' => 'Current password is incorrect.'])
                    ->withInput();
            }
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'role' => 'required|in:admin,superadmin',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update basic info
        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        // Handle password change
        if ($request->filled('password')) {
            $admin->update(['password' => Hash::make($request->password)]);
            
            // Log out from all devices
            DB::table('sessions')->where('user_id', $admin->id)->delete();
            
            // Send password reset notification
            try {
                $admin->notify(new AdminPasswordResetByAdminNotification(
                    Auth::user()->name,
                    null
                ));
            } catch (\Exception $e) {
                Log::error('Failed to send password reset notification', [
                    'admin_id' => $admin->id,
                    'error' => $e->getMessage()
                ]);
            }
            
            Log::warning('Admin password reset by superadmin', [
                'target_admin_id' => $admin->id,
                'target_admin_email' => $admin->email,
                'reset_by_id' => Auth::id(),
                'reset_by_name' => Auth::user()->name,
                'ip_address' => $request->ip()
            ]);
        }

        // Handle email change - TWO-STEP FLOW WITH TWO NOTIFICATIONS
        if ($emailChanged) {
            // Generate secure token
            $token = Str::random(60);
            
            // Delete any existing email change tokens for this admin
            DB::table('email_change_tokens')
                ->where('user_id', $admin->id)
                ->delete();
            
            // Store the email change request
            DB::table('email_change_tokens')->insert([
                'user_id' => $admin->id,
                'old_email' => $oldEmail,
                'new_email' => $request->email,
                'token' => hash('sha256', $token),
                'created_at' => now()
            ]);

            Log::info('Email change initiated for admin - preparing notifications', [
                'admin_id' => $admin->id,
                'old_email' => $oldEmail,
                'new_email' => $request->email,
                'changed_by' => Auth::user()->name
            ]);

            // SEND 1: Confirmation email to OLD email address (with link to confirm)
            try {
                Notification::route('mail', $oldEmail)
                    ->notify(new EmailChangeNotification(
                        $admin,
                        $oldEmail,
                        $request->email,
                        Auth::user()->name . ' (Super Admin)',
                        $token,
                        'confirmation' // Type: confirmation
                    ));
                    
                Log::info('Email change confirmation sent to old address for admin', [
                    'admin_id' => $admin->id,
                    'old_email' => $oldEmail,
                    'new_email' => $request->email
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send email change confirmation to admin', [
                    'admin_id' => $admin->id,
                    'old_email' => $oldEmail,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // Revert email update on failure
                $admin->email = $oldEmail;
                $admin->save();
                
                // Delete the token
                DB::table('email_change_tokens')
                    ->where('user_id', $admin->id)
                    ->delete();
                
                return redirect()->back()
                    ->withErrors([
                        'email' => 'Failed to send confirmation email to ' . $oldEmail . '. Please try again.'
                    ])
                    ->withInput();
            }

            // SEND 2: Informational email to NEW email address (notifying about the change)
            try {
                Notification::route('mail', $request->email)
                    ->notify(new EmailChangeNotification(
                        $admin,
                        $oldEmail,
                        $request->email,
                        Auth::user()->name . ' (Super Admin)',
                        null,
                        'notification' // Type: notification
                    ));
                    
                Log::info('Email change notification sent to new email for admin', [
                    'admin_id' => $admin->id,
                    'old_email' => $oldEmail,
                    'new_email' => $request->email
                ]);
            } catch (\Exception $e) {
                Log::warning('Failed to send email change notification to new email for admin', [
                    'admin_id' => $admin->id,
                    'new_email' => $request->email,
                    'error' => $e->getMessage()
                ]);
                // Don't fail the transaction for this - it's informational only
            }
            
            // IMPORTANT: Revert email to original - will be updated after confirmation
            $admin->email = $oldEmail;
            $admin->save();
            
            Log::info('Email change awaiting confirmation for admin', [
                'admin_id' => $admin->id,
                'old_email' => $oldEmail,
                'new_email' => $request->email,
                'status' => 'pending_confirmation'
            ]);
        }

        $this->logActivity('updated', 'User', $admin->id, [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'email_changed' => $emailChanged
        ]);

        $successMessage = 'Admin updated successfully.';
        if ($emailChanged) {
            $successMessage .= ' A confirmation link has been sent to the old email address (' . $oldEmail . '). The admin must click this link to confirm the email change. A notification has also been sent to the new email address (' . $request->email . ').';
        }

        return redirect()->route('admin.admins.index')
                        ->with('success', $successMessage);
    }

    public function resendVerificationEmail(User $admin)
    {
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        if ($admin->hasVerifiedEmail()) {
            return redirect()->route('admin.admins.index')
                            ->with('info', 'This admin email is already verified.');
        }

        $admin->sendEmailVerificationNotification();

        $this->logActivity('resent_verification', 'User', $admin->id, [
            'email' => $admin->email
        ]);

        return redirect()->route('admin.admins.index')
                        ->with('success', "Verification email resent to {$admin->email}");
    }

    /**
     * Remove the specified admin from storage.
     */
    public function destroy(User $admin)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        if ($admin->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete yourself.'
            ], 400);
        }

        try {
            \App\Services\RecycleBinService::softDelete(
                $admin,
                'Deleted from admin users'
            );

            $this->logActivity('deleted', 'User', $admin->id, [
                'name' => $admin->name,
                'email' => $admin->email,
                'action' => 'moved_to_recycle_bin'
            ]);

            \Log::info('Admin user moved to recycle bin', [
                'admin_id' => $admin->id,
                'admin_name' => $admin->name,
                'admin_email' => $admin->email,
                'deleted_by' => auth()->user()->name ?? 'System'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Admin user moved to recycle bin successfully.'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error moving admin to recycle bin', [
                'admin_id' => $admin->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting admin: ' . $e->getMessage()
            ], 500);
        }
    }
}