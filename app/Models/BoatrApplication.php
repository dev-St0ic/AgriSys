<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Traits\SendsApplicationSms;

class BoatrApplication extends Model
{
    use HasFactory, SoftDeletes, SendsApplicationSms;

    protected $fillable = [
        'user_id', // Foreign key to user_registration table
        'application_number',
        'first_name',
        'middle_name',
        'last_name',
        'name_extension',
        'contact_number',
        'email',
        'barangay',
        'fishr_number',
        'vessel_name',
        'boat_type',
        'boat_length',
        'boat_width',
        'boat_depth',
        'engine_type',
        'engine_horsepower',
        'primary_fishing_gear',

        // UPDATED: Single User Document
        'user_document_path',
        'user_document_name',
        'user_document_type',
        'user_document_size',
        'user_document_uploaded_at',

        // Multiple Inspection Documents
        'inspection_documents',
        'inspection_completed',
        'inspection_date',
        'inspection_notes',
        'inspected_by',
        'documents_verified',
        'documents_verified_at',
        'document_verification_notes',

        // Status and workflow
        'status',
        'remarks',
        'reviewed_at',
        'reviewed_by',
        'status_history',
        'inspection_scheduled_at',
        'approved_at',
        'rejected_at'
    ];

    protected $casts = [
        'inspection_date' => 'datetime',
        'reviewed_at' => 'datetime',
        'documents_verified_at' => 'datetime',
        'inspection_scheduled_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'user_document_uploaded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',

        'inspection_completed' => 'boolean',
        'documents_verified' => 'boolean',

        'boat_length' => 'decimal:2',
        'boat_width' => 'decimal:2',
        'boat_depth' => 'decimal:2',
        'engine_horsepower' => 'integer',
        'user_document_size' => 'integer',

        // JSON fields
        'inspection_documents' => 'array',
        'status_history' => 'array'
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
            'pending' => 'Pending Review',
            'under_review' => 'Under Review',
            'inspection_scheduled' => 'Inspection Scheduled',
            'inspection_required' => 'Inspection Required',
            'documents_pending' => 'Documents Pending',
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
            'pending' => 'secondary',
            'under_review' => 'info',
            'inspection_scheduled' => 'primary',
            'inspection_required' => 'warning',
            'documents_pending' => 'warning',
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
     * Relationship: BoatR application belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(UserRegistration::class, 'user_id');
    }

    /**
     * Relationship with admin who reviewed the application
     */
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Relationship with admin who conducted inspection
     */
    public function inspector()
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    // ==============================================
    // DOCUMENT MANAGEMENT METHODS - FIXED AND ENHANCED
    // ==============================================

    /**
     * Set user document (single file only)
     */
    public function setUserDocument($filePath, $originalName, $fileSize = null)
    {
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        $this->update([
            'user_document_path' => $filePath,
            'user_document_name' => $originalName,
            'user_document_type' => $extension,
            'user_document_size' => $fileSize,
            'user_document_uploaded_at' => now()
        ]);
    }

    /**
     * Add inspection document to array
     */
    public function addInspectionDocument($filePath, $originalName = null, $type = null, $notes = null)
    {
        $documents = $this->inspection_documents ?? [];
        $documents[] = [
            'path' => $filePath,
            'original_name' => $originalName,
            'type' => $type ?: pathinfo($filePath, PATHINFO_EXTENSION),
            'uploaded_at' => now()->toISOString(),
            'uploaded_by' => auth()->id(),
            'notes' => $notes,
            'size' => Storage::disk('public')->exists($filePath) ? Storage::disk('public')->size($filePath) : null
        ];

        $this->update([
            'inspection_documents' => $documents
        ]);
    }

    /**
     * Get user document with URL (single document) - FIXED
     */
    public function getUserDocumentWithUrl()
    {
        if (!$this->user_document_path) return null;

        $exists = Storage::disk('public')->exists($this->user_document_path);

        return [
            'path' => $this->user_document_path,
            'original_name' => $this->user_document_name ?: 'User Document',
            'type' => $this->user_document_type ?: pathinfo($this->user_document_path, PATHINFO_EXTENSION),
            'size' => $this->user_document_size,
            'uploaded_at' => $this->user_document_uploaded_at?->format('M d, Y h:i A'),
            'url' => $exists ? asset('storage/' . $this->user_document_path) : null,
            'exists' => $exists
        ];
    }

    /**
     * Get all inspection documents with URLs - ENHANCED
     */
    public function getInspectionDocumentsWithUrls()
    {
        if (!$this->inspection_documents) return [];

        return collect($this->inspection_documents)->map(function ($doc, $index) {
            $exists = Storage::disk('public')->exists($doc['path']);

            return array_merge($doc, [
                'index' => $index,
                'url' => $exists ? asset('storage/' . $doc['path']) : null,
                'exists' => $exists,
                'extension' => $doc['type'] ?? pathinfo($doc['path'], PATHINFO_EXTENSION),
                'uploader' => isset($doc['uploaded_by']) ? User::find($doc['uploaded_by'])?->name : null,
                'formatted_size' => $this->formatFileSize($doc['size'] ?? 0),
                'uploaded_at_formatted' => isset($doc['uploaded_at']) ?
                    \Carbon\Carbon::parse($doc['uploaded_at'])->format('M d, Y h:i A') : null
            ]);
        })->toArray();
    }

    /**
     * Check if has user document
     */
    public function hasUserDocument()
    {
        return !empty($this->user_document_path) && Storage::disk('public')->exists($this->user_document_path);
    }

    /**
     * Check if has inspection documents
     */
    public function hasInspectionDocuments()
    {
        return !empty($this->inspection_documents) && count($this->inspection_documents) > 0;
    }

    /**
     * Count total documents - FIXED
     */
    public function getTotalDocumentsCountAttribute()
    {
        $userDocCount = $this->hasUserDocument() ? 1 : 0;
        $inspectionDocCount = count($this->inspection_documents ?? []);
        return $userDocCount + $inspectionDocCount;
    }

    /**
     * Format file size - UTILITY METHOD
     */
    private function formatFileSize($bytes)
    {
        if (!$bytes || $bytes === 0) return 'Unknown size';

        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log(1024));
        return round($bytes / pow(1024, $i) * 100) / 100 . ' ' . $sizes[$i];
    }

    /**
     * Get document by type and index - NEW METHOD
     */
    public function getDocument($type, $index = 0)
    {
        if ($type === 'user') {
            return $this->getUserDocumentWithUrl();
        } else {
            $inspectionDocs = $this->getInspectionDocumentsWithUrls();
            return isset($inspectionDocs[$index]) ? $inspectionDocs[$index] : null;
        }
    }

    /**
     * Delete user document - NEW METHOD
     */
    public function deleteUserDocument()
    {
        if ($this->user_document_path && Storage::disk('public')->exists($this->user_document_path)) {
            Storage::disk('public')->delete($this->user_document_path);
        }

        $this->update([
            'user_document_path' => null,
            'user_document_name' => null,
            'user_document_type' => null,
            'user_document_size' => null,
            'user_document_uploaded_at' => null
        ]);
    }

    /**
     * Delete inspection document by index - NEW METHOD
     */
    public function deleteInspectionDocument($index)
    {
        $documents = $this->inspection_documents ?? [];

        if (isset($documents[$index])) {
            $document = $documents[$index];

            // Delete file
            if (Storage::disk('public')->exists($document['path'])) {
                Storage::disk('public')->delete($document['path']);
            }

            // Remove from array
            array_splice($documents, $index, 1);

            $this->update([
                'inspection_documents' => $documents
            ]);
        }
    }

    // ==============================================
    // STATUS WORKFLOW METHODS
    // ==============================================

    /**
     * Update status with history tracking
     */
    public function updateStatus($newStatus, $remarks = null, $adminId = null)
    {
        $oldStatus = $this->status;
        $history = $this->status_history ?? [];

        $history[] = [
            'from_status' => $oldStatus,
            'to_status' => $newStatus,
            'changed_at' => now()->toISOString(),
            'changed_by' => $adminId ?? auth()->id(),
            'changed_by_name' => $adminId ? User::find($adminId)?->name : auth()->user()?->name,
            'remarks' => $remarks
        ];

        $updateData = [
            'status' => $newStatus,
            'status_history' => $history,
            'reviewed_at' => now(),
            'reviewed_by' => $adminId ?? auth()->id()
        ];

        if ($remarks) {
            $updateData['remarks'] = $remarks;
        }

        // Set specific timestamps based on status
        switch ($newStatus) {
            case 'approved':
                $updateData['approved_at'] = now();
                break;
            case 'rejected':
                $updateData['rejected_at'] = now();
                break;
            case 'inspection_scheduled':
                $updateData['inspection_scheduled_at'] = now();
                break;
        }

        $this->update($updateData);
    }

    /**
     * Complete inspection
     */
    public function completeInspection($notes = null, $adminId = null)
    {
        $this->update([
            'inspection_completed' => true,
            'inspection_date' => now(),
            'inspection_notes' => $notes,
            'inspected_by' => $adminId ?? auth()->id()
        ]);
    }

    /**
     * Check if ready for approval
     */
    public function isReadyForApproval()
    {
        return $this->inspection_completed && $this->hasInspectionDocuments();
    }

    /**
     * Check if requires inspection
     */
    public function requiresInspection()
    {
        return !$this->inspection_completed &&
               in_array($this->status, ['inspection_required', 'inspection_scheduled']);
    }

    // ==============================================
    // SCOPE METHODS
    // ==============================================

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
                  ->orWhere('fishr_number', 'like', "%{$search}%")
                  ->orWhere('barangay', 'like', "%{$search}%")
                  ->orWhere('contact_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Scope for status
     */
    public function scopeStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope for boat type
     */
    public function scopeBoatType($query, $boatType)
    {
        if ($boatType) {
            return $query->where('boat_type', $boatType);
        }
        return $query;
    }

    /**
     * Scope for fishing gear
     */
    public function scopeFishingGear($query, $gear)
    {
        if ($gear) {
            return $query->where('primary_fishing_gear', $gear);
        }
        return $query;
    }

    // ==============================================
    // STATIC METHODS
    // ==============================================

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
     * Get available status options
     */
    public static function getStatusOptions()
    {
        return [
            'pending' => 'Pending Review',
            'under_review' => 'Under Review',
            'inspection_scheduled' => 'Inspection Scheduled',
            'inspection_required' => 'Inspection Required',
            'documents_pending' => 'Documents Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected'
        ];
    }

    /**
     * Get the annexes for the application
     */
    public function annexes()
    {
        return $this->hasMany(BoatrAnnex::class, 'boatr_application_id');
    }

    /**
     * Get status statistics - NEW METHOD
     */
    public static function getStatusStatistics()
    {
        return [
            'total' => self::count(),
            'pending' => self::where('status', 'pending')->count(),
            'under_review' => self::where('status', 'under_review')->count(),
            'inspection_required' => self::where('status', 'inspection_required')->count(),
            'inspection_scheduled' => self::where('status', 'inspection_scheduled')->count(),
            'documents_pending' => self::where('status', 'documents_pending')->count(),
            'approved' => self::where('status', 'approved')->count(),
            'rejected' => self::where('status', 'rejected')->count(),
            'with_user_docs' => self::whereNotNull('user_document_path')->count(),
            'with_inspection_docs' => self::whereNotNull('inspection_documents')->count(),
            'inspection_completed' => self::where('inspection_completed', true)->count()
        ];
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
        return 'BoatR Registration';
    }
}
