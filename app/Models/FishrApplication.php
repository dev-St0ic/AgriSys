<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FishrApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'fishr_applications';

    protected $fillable = [
        // Personal Information
        'first_name',
        'middle_name',
        'last_name',
        'name_extension',
        'sex',
        
        // Contact Information
        'barangay',
        'barangay_id',
        'contact_number',
        
        // Main Livelihood
        'main_livelihood',
        'livelihood_description',
        'other_livelihood',
        
        // Secondary Livelihood (NEW)
        'secondary_livelihood',
        'other_secondary_livelihood',
        
        // Supporting Documents
        'document_path',
        
        // Status Management
        'status',
        'remarks',
        'status_updated_at',
        'updated_by',
        
        // FishR Number
        'fishr_number',
        'fishr_number_assigned_at',
        'fishr_number_assigned_by',
        
        // Registration
        'registration_number',
        'user_id',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'status_updated_at' => 'datetime',
        'fishr_number_assigned_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====

    /**
     * Get the user who owns this application
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserRegistration::class, 'user_id');
    }

    /**
     * Get the admin user who updated the status
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the admin user who assigned the FishR number
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fishr_number_assigned_by');
    }

    /**
     * Get all annexes for this registration
     */
    public function annexes(): HasMany
    {
        return $this->hasMany(FishrAnnex::class, 'fishr_application_id', 'id');
    }

    /**
     * BOOT: When FishR is deleted, cascade delete to annexes
     */
    protected static function boot()
    {
        parent::boot();

        // When FishR is soft-deleted, soft-delete all its annexes
        static::deleting(function ($model) {
            if ($model->isForceDeleting()) {
                // Force delete annexes too
                $model->annexes()->forceDelete();
            } else {
                // Soft delete annexes
                $model->annexes()->delete();
            }
        });

        // When FishR is restored, restore all its annexes
        static::restored(function ($model) {
            $model->annexes()->withTrashed()->restore();
        });
    }

    // ===== ACCESSORS & MUTATORS =====

    /**
     * Get the full name attribute
     */
    public function getFullNameAttribute(): string
    {
        $name = "{$this->first_name} {$this->last_name}";
        
        if ($this->middle_name) {
            $name = "{$this->first_name} {$this->middle_name} {$this->last_name}";
        }
        
        if ($this->name_extension) {
            $name .= " {$this->name_extension}";
        }
        
        return trim($name);
    }

    /**
     * Get formatted status with label
     */
    public function getFormattedStatusAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Pending',
            'under_review' => 'Under Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => ucfirst($this->status)
        };
    }

    /**
     * Get status color for UI display
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'info',      // blue
            'under_review' => 'warning',    // yellow
            'approved' => 'success',     // Green
            'rejected' => 'danger',      // Red
            default => 'secondary'
        };
    }

    /**
     * Get livelihood description based on main livelihood
     */
    public function getLivelihoodDescriptionAttribute(): string
    {
        return match($this->main_livelihood) {
            'capture' => 'Capture Fishing',
            'aquaculture' => 'Aquaculture',
            'vending' => 'Fish Vending',
            'processing' => 'Fish Processing',
            'others' => $this->other_livelihood ?? 'Others',
            default => 'Unknown'
        };
    }

    /**
     * Get secondary livelihood description
     */
    public function getSecondaryLivelihoodDescriptionAttribute(): ?string
    {
        if (!$this->secondary_livelihood) {
            return null;
        }

        return match($this->secondary_livelihood) {
            'capture' => 'Capture Fishing',
            'aquaculture' => 'Aquaculture',
            'vending' => 'Fish Vending',
            'processing' => 'Fish Processing',
            'others' => $this->other_secondary_livelihood ?? 'Others',
            default => null
        };
    }

    /**
     * Get applicant phone number - FIXED: Removed direct method call
     * Use $model->applicant_phone instead of $model->getApplicantPhone()
     */
    public function getApplicantPhoneAttribute(): string
    {
        return $this->contact_number ?? '';
    }

    /**
     * Get applicant name - FIXED: Use as property accessor
     * Use $model->applicant_name instead of $model->getApplicantName()
     */
    public function getApplicantNameAttribute(): string
    {
        return $this->full_name;
    }

    /**
     * Get application type name - FIXED: Change to method if it's called as method
     * Keep this as a regular method since it's called as getApplicationTypeName()
     */
    public function getApplicationTypeName(): string
    {
        return 'FishR Registration';
    }

    // ===== SCOPES =====

    /**
     * Scope to get pending applications
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get under review applications
     */
    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    /**
     * Scope to get approved applications
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope to get rejected applications
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Scope to filter by barangay
     */
    public function scopeByBarangay($query, string $barangay)
    {
        return $query->where('barangay', $barangay);
    }

    /**
     * Scope to filter by main livelihood
     */
    public function scopeByMainLivelihood($query, string $livelihood)
    {
        return $query->where('main_livelihood', $livelihood);
    }

    /**
     * Scope to filter by secondary livelihood
     */
    public function scopeBySecondaryLivelihood($query, string $livelihood)
    {
        return $query->where('secondary_livelihood', $livelihood);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to search by name or contact
     */
    public function scopeSearch($query, string $searchTerm)
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('registration_number', 'like', "%{$searchTerm}%")
              ->orWhere('first_name', 'like', "%{$searchTerm}%")
              ->orWhere('last_name', 'like', "%{$searchTerm}%")
              ->orWhere('contact_number', 'like', "%{$searchTerm}%");
        });
    }

    // ===== METHODS =====

    /**
     * Check if application has secondary livelihood
     */
    public function hasSecondaryLivelihood(): bool
    {
        return !is_null($this->secondary_livelihood) && $this->secondary_livelihood !== '';
    }

    /**
     * Check if application is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if application has a FishR number assigned
     */
    public function hasFishrNumber(): bool
    {
        return !is_null($this->fishr_number);
    }

    /**
     * Check if application can be rejected (must be pending or under review)
     */
    public function canBeRejected(): bool
    {
        return in_array($this->status, ['pending', 'under_review']);
    }

    /**
     * Check if FishR number can be assigned (must be approved and not already assigned)
     */
    public function canAssignFishrNumber(): bool
    {
        return $this->status === 'approved' && !$this->hasFishrNumber();
    }

    /**
     * Get the livelihood details as array
     */
    public function getLivelihoodDetails(): array
    {
        return [
            'main' => [
                'type' => $this->main_livelihood,
                'description' => $this->livelihood_description,
                'other' => $this->other_livelihood,
            ],
            'secondary' => $this->hasSecondaryLivelihood() ? [
                'type' => $this->secondary_livelihood,
                'description' => $this->secondary_livelihood_description,
                'other' => $this->other_secondary_livelihood,
            ] : null,
        ];
    }
}