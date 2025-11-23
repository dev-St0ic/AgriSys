<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\SendsApplicationSms;

class FishrApplication extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, SendsApplicationSms;

    protected $fillable = [
        'user_id', // Foreign key to user_registration table
        'registration_number',
        'first_name',
        'middle_name',
        'last_name',
        'name_extension',
        'sex',
        'barangay',
        'contact_number',
        'email',
        'main_livelihood',
        'livelihood_description',
        'other_livelihood',
        'document_path',
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
                  ->orWhere('registration_number', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Relationship: FishR application belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(UserRegistration::class, 'user_id');
    }

    /**
     * Relationship: FishR application has many annexes
     */
    public function annexes()
    {
        return $this->hasMany(FishrAnnex::class, 'fishr_application_id');
    }

    /**
     * Check if the application has a supporting document
     */
    public function hasDocument()
    {
        return !empty($this->document_path) && \Storage::disk('public')->exists($this->document_path);
    }

    /**
     * Get the document URL if it exists
     */
    public function getDocumentUrlAttribute()
    {
        if ($this->hasDocument()) {
            return asset('storage/' . $this->document_path);
        }
        return null;
    }

    /**
     * Get the file extension of the document
     */
    public function getDocumentExtensionAttribute()
    {
        if ($this->document_path) {
            return strtolower(pathinfo($this->document_path, PATHINFO_EXTENSION));
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'remarks', 'updated_by'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get applicant phone number for SMS notifications
     */
    protected function getApplicantPhone(): ?string
    {
        return $this->contact_number;
    }

    /**
     * Get applicant name for SMS notifications
     */
    protected function getApplicantName(): ?string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get application type name for SMS notifications
     */
    protected function getApplicationTypeName(): string
    {
        return 'FishR Registration';
    }
}
