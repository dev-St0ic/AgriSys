<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Notifications\VerifyAdminEmail;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_photo',
        'contact_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     /**
     * Get the profile photo URL
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo) {
            // Try direct public URL first
            $publicPath = public_path('storage/' . $this->profile_photo);
            if (file_exists($publicPath)) {
                return asset('storage/' . $this->profile_photo);
            }
            
            // Fallback to storage disk URL
            if (Storage::disk('public')->exists($this->profile_photo)) {
                return Storage::disk('public')->url($this->profile_photo);
            }
        }
        return null;
    }

    /**
     * Check if the user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is a superadmin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * Check if the user has admin privileges (admin or superadmin)
     */
    public function hasAdminPrivileges(): bool
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }


    public function sendEmailVerificationNotification()
    {
        \Log::info('DEBUG: About to send verification email to ' . $this->email);
        try {
            $this->notify(new VerifyAdminEmail());
            \Log::info('DEBUG: Verification email sent successfully to ' . $this->email);
        } catch (\Exception $e) {
            \Log::error('DEBUG: Failed to send verification email: ' . $e->getMessage());
        }
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
