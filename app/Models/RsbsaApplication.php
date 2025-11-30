<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\SendsApplicationSms;

class RsbsaApplication extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, SendsApplicationSms;

    protected $fillable = [
        'user_id', // Foreign key to user_registration table
        'application_number',
        'first_name',
        'middle_name',
        'last_name',
        'name_extension',
        'sex',
        'contact_number',
        'barangay',
        'main_livelihood',
        'land_area',
        'farm_location',
        'commodity',
        'supporting_document_path',
        'status',
        'remarks',
        'reviewed_at',
        'reviewed_by',
        'approved_at',
        'rejected_at',
        'number_assigned_at',
        'assigned_by'
    ];

    protected $casts = [
        'land_area' => 'decimal:2',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'number_assigned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];





    /**
     * Get the full name attribute
     */
    public function getFullNameAttribute()
    {
        $parts = array_filter([
            $this->first_name,
            $this->middle_name,
            $this->last_name
        ]);
        return implode(' ', $parts);
    }

    /**
     * Get the contact number (alias for contact_number for backward compatibility)
     */
    public function getContactNumberAttribute()
    {
        return $this->attributes['contact_number'];
    }

    /**
     * Get the document path (alias for supporting_document_path for backward compatibility)
     */
    public function getDocumentPathAttribute()
    {
        return $this->supporting_document_path;
    }

    /**
     * Get the formatted status for display
     */
    public function getFormattedStatusAttribute()
    {
        return match($this->status) {
            'pending' => 'Pending',
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
            'pending' => 'info',
            'under_review' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Relationship: RSBSA application belongs to a user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserRegistration::class, 'user_id');
    }


    /**
     * Relationship with admin who reviewed the application
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Relationship with admin who assigned the number
     */
    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
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
     * Scope for filtering by livelihood
     */
    public function scopeWithLivelihood($query, $livelihood)
    {
        if ($livelihood) {
            return $query->where('main_livelihood', $livelihood);
        }
        return $query;
    }

    /**
     * Scope for filtering by barangay
     */
    public function scopeWithBarangay($query, $barangay)
    {
        if ($barangay) {
            return $query->where('barangay', $barangay);
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
                  ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Check if the application has a supporting document
     */
    public function hasDocument()
    {
        return !empty($this->supporting_document_path) && \Storage::disk('public')->exists($this->supporting_document_path);
    }

    /**
     * Get the document URL if it exists
     */
    public function getDocumentUrlAttribute()
    {
        if ($this->hasDocument()) {
            return asset('storage/' . $this->supporting_document_path);
        }
        return null;
    }

    /**
     * Get the file extension of the document
     */
    public function getDocumentExtensionAttribute()
    {
        if ($this->supporting_document_path) {
            return strtolower(pathinfo($this->supporting_document_path, PATHINFO_EXTENSION));
        }
        return null;
    }

    /**
     * Check if document is an image
     */
    public function isDocumentImage()
    {
        return in_array($this->document_extension, ['jpg', 'jpeg', 'png', 'gif']);
    }

    /**
     * Check if document is a PDF
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
        return 'RSBSA Registration';
    }
}
