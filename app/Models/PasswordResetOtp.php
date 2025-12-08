<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    use HasFactory;

    protected $table = 'password_reset_otps';

    protected $fillable = [
        'contact_number',
        'otp',
        'expires_at',
        'is_verified',
        'attempts',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    /**
     * Check if the OTP has expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Check if max attempts reached (3 attempts)
     */
    public function hasMaxAttempts(): bool
    {
        return $this->attempts >= 3;
    }

    /**
     * Increment the attempt counter
     */
    public function incrementAttempts(): void
    {
        $this->increment('attempts');
    }

    /**
     * Mark OTP as verified
     */
    public function markAsVerified(): void
    {
        $this->update(['is_verified' => true]);
    }

    /**
     * Scope to find valid (non-expired, non-verified) OTPs
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now())
                     ->where('is_verified', false)
                     ->where('attempts', '<', 3);
    }

    /**
     * Scope to find by contact number
     */
    public function scopeForContact($query, $contactNumber)
    {
        return $query->where('contact_number', $contactNumber);
    }
}
