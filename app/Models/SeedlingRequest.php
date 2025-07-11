<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeedlingRequest extends Model
{
    protected $fillable = [
        'request_number',
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'contact_number',
        'address',
        'barangay',
        'planting_location',
        'purpose',
        'seedling_type',
        'requested_quantity',
        'preferred_delivery_date',
        'document_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'remarks',
        'approved_quantity',
        'approved_at',
        'rejected_at'
    ];

    protected $casts = [
        'preferred_delivery_date' => 'date',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'requested_quantity' => 'integer',
        'approved_quantity' => 'integer'
    ];

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function getFullNameAttribute(): string
    {
        $fullName = trim($this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name);
        if ($this->extension_name) {
            $fullName .= ' ' . $this->extension_name;
        }
        return $fullName;
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'approved' => 'success',
            'rejected' => 'danger',
            'under_review' => 'warning',
            default => 'secondary'
        };
    }
}
