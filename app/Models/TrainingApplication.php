<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\SendsApplicationSms;

class TrainingApplication extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, SendsApplicationSms;

    protected $fillable = [
        'user_id',
        'application_number',
        'first_name',
        'middle_name',
        'last_name',
        'name_extension',
        'contact_number',
        'email',
        'barangay',
        'training_type',
        'document_path', // CHANGED: Single path instead of array
        'status',
        'remarks',
        'status_updated_at',
        'updated_by'
    ];

    protected $casts = [
        'status_updated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the full name attribute
     */
    public function getFullNameAttribute()
    {
        $parts = array_filter([$this->first_name, $this->middle_name, $this->last_name]);
        return implode(' ', $parts);
    }

    /**
     * Get the formatted status for display
     */
    public function getFormattedStatusAttribute()
    {
        return match($this->status) {
            'under_review' => 'Under Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => ucfirst(str_replace('_', ' ', $this->status))
        };
    }

    /**
     * Get the status color for badges
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'under_review' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get the training type display name
     */
    public function getTrainingTypeDisplayAttribute()
    {
        return match($this->training_type) {
            'tilapia_hito' => 'Tilapia and Hito Training',
            'hydroponics' => 'Hydroponics Training',
            'aquaponics' => 'Aquaponics Training',
            'mushrooms' => 'Mushrooms Production Training',
            'livestock_poultry' => 'Livestock and Poultry Training',
            'high_value_crops' => 'High Value Crops Training',
            'sampaguita_propagation' => 'Sampaguita Propagation Training',
            default => ucfirst(str_replace('_', ' ', $this->training_type))
        };
    }

    /**
     * Relationship: Training application belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(UserRegistration::class, 'user_id');
    }

    /**
     * Relationship with admin who updated the status
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for filtering by status
     */
    public function scopeWithStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope for filtering by training type
     */
    public function scopeWithTrainingType($query, $trainingType)
    {
        if ($trainingType) {
            return $query->where('training_type', $trainingType);
        }
        return $query;
    }

    /**
     * Scope for searching
     */
    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('middle_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('application_number', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * CHANGED: Check if the application has a supporting document
     */
    public function hasDocument()
    {
        return !empty($this->document_path);
    }

    /**
     * CHANGED: Get document URL if it exists
     */
    public function getDocumentUrlAttribute()
    {
        if ($this->hasDocument() && \Storage::disk('public')->exists($this->document_path)) {
            return asset('storage/' . $this->document_path);
        }
        return null;
    }

    /**
     * CHANGED: Get the file extension of the document
     */
    public function getDocumentExtensionAttribute()
    {
        if ($this->hasDocument()) {
            return strtolower(pathinfo($this->document_path, PATHINFO_EXTENSION));
        }
        return null;
    }

    /**
     * CHANGED: Check if document is an image
     */
    public function isDocumentImage()
    {
        return in_array($this->document_extension, ['jpg', 'jpeg', 'png', 'gif']);
    }

    /**
     * CHANGED: Check if document is a PDF
     */
    public function isDocumentPdf()
    {
        return $this->document_extension === 'pdf';
    }

    // Log activity options
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get applicant phone number for SMS notifications
     */
    public function getApplicantPhone(): ?string
    {
        return $this->contact_number;
    }

    /**
     * Get applicant name for SMS notifications
     */
    public function getApplicantName(): ?string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get application type name for SMS notifications
     */
    public function getApplicationTypeName(): string
    {
        return 'Training Application';
    }
}
