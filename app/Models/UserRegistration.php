<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Events\AccountVerificationStatusChanged;

class UserRegistration extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'user_registration';

    protected $fillable = [
        // Basic signup fields
        'username',
        'contact_number', // Required for signup
        'email', // Optional
        'password',
        'status',
        'terms_accepted',
        'privacy_accepted',

        // Verification fields
        'first_name',
        'middle_name',
        'last_name',
        'name_extension',
        'sex',
        'complete_address',
        'barangay',
        'user_type',
        'age',
        'date_of_birth',
        'gender',

        // Emergency contact fields
        'emergency_contact_name',
        'emergency_contact_phone',

        'profile_image_url',

        // Document paths
        'location_document_path',
        'id_front_path',
        'id_back_path',

        // System fields
        'verification_token',
        'email_verified_at',
        'approved_at',
        'rejected_at',
        'approved_by',
        'rejection_reason',
        'last_login_at',
        'username_changed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'date_of_birth' => 'date',
        'sex' => 'string', 
        'last_login_at' => 'datetime',
        'username_changed_at' => 'datetime',
        'terms_accepted' => 'boolean',
        'privacy_accepted' => 'boolean',
    ];

    // Status constants
    const STATUS_UNVERIFIED = 'unverified';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Mutators
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        $name = trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
        if ($this->name_extension) {
            $name .= ', ' . $this->name_extension;
        }
        return $name ?: $this->username;
    }

    public function getAgeFromDobAttribute()
    {
        if (!$this->date_of_birth) {
            return null;
        }
        return $this->date_of_birth->diffInYears(now());
    }

    public function getIsCompleteProfileAttribute()
    {
        return !empty($this->first_name)
            && !empty($this->last_name)
            && !empty($this->contact_number)
            && !empty($this->complete_address)
            && !empty($this->barangay)
            && !empty($this->user_type)
            && !empty($this->date_of_birth);
    }

    public function canChangeUsername()
    {
        return $this->username_changed_at === null;
    }

    public function getUsernameChangeHistory()
    {
        if ($this->username_changed_at) {
            return [
                'changed' => true,
                'changed_at' => $this->username_changed_at,
                'can_change_again' => false,
                'message' => 'Username was changed on ' . $this->username_changed_at->format('M d, Y g:i A')
            ];
        }

        return [
            'changed' => false,
            'changed_at' => null,
            'can_change_again' => true,
            'message' => 'Username can be changed once'
        ];
    }

    // Relationships
    public function approvedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    // Scopes
    public function scopeUnverified($query)
    {
        return $query->where('status', self::STATUS_UNVERIFIED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeEmailVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopeEmailUnverified($query)
    {
        return $query->whereNull('email_verified_at');
    }

    public function scopeUsernameNotChanged($query)
    {
        return $query->whereNull('username_changed_at');
    }

    public function scopeUsernameChanged($query)
    {
        return $query->whereNotNull('username_changed_at');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('username', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('first_name', 'LIKE', "%{$search}%")
              ->orWhere('last_name', 'LIKE', "%{$search}%")
              ->orWhere('contact_number', 'LIKE', "%{$search}%");
        });
    }

    public function scopeByUserType($query, $userType)
    {
        return $query->where('user_type', $userType);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Status check methods
    public function isUnverified()
    {
        return $this->status === self::STATUS_UNVERIFIED;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    public function hasCompleteProfile()
    {
        return $this->is_complete_profile;
    }

    // Status transition methods
    public function markAsPending()
    {
        $this->update(['status' => self::STATUS_PENDING]);
    }

    public function approve($adminId = null)
    {
        $previousStatus = $this->status;

        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => $adminId,
            'rejection_reason' => null,
        ]);

        // Fire event for SMS notification
        if ($previousStatus !== self::STATUS_APPROVED) {
            \Log::info('Account verification status changed - firing SMS notification event', [
                'user_id' => $this->id,
                'email' => $this->email,
                'previous_status' => $previousStatus,
                'new_status' => self::STATUS_APPROVED
            ]);
            event(new AccountVerificationStatusChanged($this, $previousStatus, self::STATUS_APPROVED));
        } else {
            \Log::info('Account verification status unchanged - skipping SMS notification', [
                'user_id' => $this->id,
                'email' => $this->email,
                'status' => self::STATUS_APPROVED
            ]);
        }
    }

    public function reject($reason = null, $adminId = null)
    {
        $previousStatus = $this->status;

        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_at' => now(),
            'approved_by' => $adminId,
            'rejection_reason' => $reason,
        ]);

        // Fire event for SMS notification
        if ($previousStatus !== self::STATUS_REJECTED) {
            event(new AccountVerificationStatusChanged($this, $previousStatus, self::STATUS_REJECTED, $reason));
        }
    }

    public function markEmailAsVerified()
    {
        if (is_null($this->email_verified_at)) {
            $this->update([
                'email_verified_at' => now(),
                'verification_token' => null,
            ]);
        }
    }

    // Helper methods
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            self::STATUS_UNVERIFIED => 'Unverified',
            self::STATUS_PENDING => 'Pending Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            self::STATUS_UNVERIFIED => 'warning',
            self::STATUS_PENDING => 'info',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
            default => 'secondary',
        };
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->date_of_birth && !$model->age) {
                $model->age = $model->date_of_birth->diffInYears(now());
            }
        });
    }

    public function rsbsaApplications()
    {
        return $this->hasMany(RsbsaApplication::class, 'user_id');
    }

    public function latestRsbsaApplication()
    {
        return $this->hasOne(RsbsaApplication::class, 'user_id')->latestOfMany();
    }

    // Log activity options
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
