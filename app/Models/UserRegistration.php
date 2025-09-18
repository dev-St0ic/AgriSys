<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class UserRegistration extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $table = 'user_registration';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'status',
        'phone',
        'address',
        'user_type',
        'verification_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Automatically hash password when setting
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    // Get full name attribute
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Check if registration is pending
    public function isPending()
    {
        return $this->status === 'pending';
    }

    // Check if registration is approved
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    // Check if registration is rejected
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    // Check if email is verified
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    // Mark email as verified
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
            'verification_token' => null,
        ])->save();
    }

    // Approve registration
    public function approve($adminId = null)
    {
        return $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $adminId,
        ]);
    }

    // Reject registration
    public function reject($reason = null, $adminId = null)
    {
        return $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'approved_by' => $adminId,
        ]);
    }

    // Relationship with admin who approved/rejected
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scope for filtering by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope for pending registrations
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for approved registrations
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Scope for rejected registrations
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Scope for email verified
    public function scopeEmailVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    // Scope for unverified emails
    public function scopeEmailUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }
}