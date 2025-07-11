<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RsbsaApplication extends Model
{
    protected $fillable = [
        'application_number',
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'sex',
        'house_lot_number',
        'street_sitio_subdivision',
        'barangay',
        'city_municipality',
        'province',
        'region',
        'contact_number',
        'date_of_birth',
        'place_of_birth',
        'civil_status',
        'spouse_name',
        'highest_formal_education',
        'persons_with_disability',
        'government_id',
        'pwd_id_number',
        'is_indigenous',
        'indigenous_specify',
        'with_government_id',
        'government_id_specify',
        'government_id_number',
        'emergency_contact_person',
        'emergency_contact_number',
        'farm_parcel_area',
        'farm_location',
        'north_boundary',
        'south_boundary',
        'east_boundary',
        'west_boundary',
        'tenurial_status',
        'document_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'remarks',
        'approved_at',
        'rejected_at'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'persons_with_disability' => 'boolean',
        'is_indigenous' => 'boolean',
        'with_government_id' => 'boolean'
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
