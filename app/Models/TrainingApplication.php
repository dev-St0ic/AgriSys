<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_number',
        'first_name',
        'middle_name',
        'last_name',
        'contact_number',
        'email',
        'barangay',
        'training_type',
        'document_paths',
        'status',
        'remarks',
        'status_updated_at',
        'updated_by'
    ];

    protected $casts = [
        'document_paths' => 'array',
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
     * Check if the application has supporting documents
     */
    public function hasDocuments()
    {
        return !empty($this->document_paths) && is_array($this->document_paths);
    }

    /**
     * Get document URLs if they exist
     */
    public function getDocumentUrlsAttribute()
    {
        if ($this->hasDocuments()) {
            return collect($this->document_paths)->map(function($path) {
                return \Storage::disk('public')->exists($path) ? asset('storage/' . $path) : null;
            })->filter()->values();
        }
        return collect();
    }

    /**
     * Get the file extension of the first document
     */
    public function getFirstDocumentExtensionAttribute()
    {
        if ($this->hasDocuments() && isset($this->document_paths[0])) {
            return strtolower(pathinfo($this->document_paths[0], PATHINFO_EXTENSION));
        }
        return null;
    }

    /**
     * Check if first document is an image
     */
    public function isFirstDocumentImage()
    {
        return in_array($this->first_document_extension, ['jpg', 'jpeg', 'png', 'gif']);
    }

    /**
     * Check if first document is a PDF
     */
    public function isFirstDocumentPdf()
    {
        return $this->first_document_extension === 'pdf';
    }
}
