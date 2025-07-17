<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoatrApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'application_number',
        'first_name',
        'middle_name',
        'last_name',
        'fishr_number',
        'vessel_name',
        'boat_type',
        'boat_length',
        'boat_width', 
        'boat_depth',
        'engine_type',
        'engine_horsepower',
        'primary_fishing_gear',
        'supporting_document_path',
        'inspection_completed',
        'inspection_date',
        'status',
        'remarks',
        'reviewed_at',
        'reviewed_by'
    ];

    protected $casts = [
        'inspection_date' => 'datetime',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'inspection_completed' => 'boolean',
        'boat_length' => 'decimal:2',
        'boat_width' => 'decimal:2',
        'boat_depth' => 'decimal:2',
        'engine_horsepower' => 'integer'
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
            'pending' => 'Pending',
            'inspection_required' => 'Inspection Required',
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
            'inspection_required' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get boat dimensions formatted string
     */
    public function getBoatDimensionsAttribute()
    {
        return "{$this->boat_length}' × {$this->boat_width}' × {$this->boat_depth}'";
    }

    /**
     * Relationship with admin who reviewed the application
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
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
     * Scope for filtering by boat type
     */
    public function scopeWithBoatType($query, $boatType)
    {
        if ($boatType) {
            return $query->where('boat_type', $boatType);
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
                  ->orWhere('vessel_name', 'like', "%{$search}%")
                  ->orWhere('fishr_number', 'like', "%{$search}%");
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

    /**
     * Get available boat types
     */
    public static function getBoatTypes()
    {
        return [
            'Spoon',
            'Plumb',
            'Banca',
            'Rake Stem - Rake Stern',
            'Rake Stem - Transom/Spoon/Plumb Stern',
            'Skiff (Typical Design)'
        ];
    }

    /**
     * Get available fishing gear types
     */
    public static function getFishingGearTypes()
    {
        return [
            'Hook and Line',
            'Bottom Set Gill Net',
            'Fish Trap',
            'Fish Coral'
        ];
    }

    /**
     * Check if inspection is required for this application
     */
    public function requiresInspection()
    {
        return $this->status === 'inspection_required' || !$this->inspection_completed;
    }

    /**
     * Check if application can be approved (inspection completed)
     */
    public function canBeApproved()
    {
        return $this->inspection_completed && $this->hasDocument();
    }
}