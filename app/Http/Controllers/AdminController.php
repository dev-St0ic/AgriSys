<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AdminController extends Controller
{
    /**
     * Display a listing of the admins.
     */
    public function index()
    {
        // Only superadmin can manage admins
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

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,superadmin',
        ]);

        // User::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password),
        //     'role' => $request->role,
        // ]);

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

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'role' => 'required|in:admin,superadmin',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $emailChanged = $request->email !== $admin->email;

        $admin->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $admin->update(['password' => Hash::make($request->password)]);
        }

        if ($emailChanged) {
            $admin->update(['email_verified_at' => null]);
            $admin->sendEmailVerificationNotification();
        }

        $this->logActivity('updated', 'User', $admin->id, [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'email_changed' => $emailChanged
        ]);

        $successMessage = 'Admin updated successfully.';
        if ($emailChanged) {
            $successMessage .= ' Verification email has been sent to ' . $admin->email;
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
            abort(403, 'Unauthorized action.');
        }

        // Prevent deleting yourself
        if ($admin->id === Auth::id()) {
            return redirect()->route('admin.admins.index')
                            ->with('error', 'You cannot delete yourself.');
        }

        $this->logActivity('deleted', 'User', $admin->id, [
            'name' => $admin->name,
            'email' => $admin->email
        ]);

        $admin->delete();

        return redirect()->route('admin.admins.index')
                        ->with('success', 'Admin deleted successfully.');
    }
}
