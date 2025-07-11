<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FishrApplication extends Model
{
    protected $fillable = [
        'application_number',
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'contact_number',
        'address',
        'barangay',
        'years_of_experience',
        'previous_recipient',
        'previous_year',
        'aquaculture_area',
        'water_source',
        'fish_species',
        'requested_fingerlings',
        'purpose',
        'preferred_delivery_date',
        'document_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'remarks',
        'approved_fingerlings',
        'approved_at',
        'rejected_at'
    ];

    protected $casts = [
        'preferred_delivery_date' => 'date',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'years_of_experience' => 'integer',
        'previous_year' => 'integer',
        'previous_recipient' => 'boolean',
        'requested_fingerlings' => 'integer',
        'approved_fingerlings' => 'integer'
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
