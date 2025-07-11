<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barangay extends Model
{
    protected $fillable = [
        'name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function rsbsaApplications(): HasMany
    {
        return $this->hasMany(RsbsaApplication::class, 'barangay', 'name');
    }

    public function seedlingRequests(): HasMany
    {
        return $this->hasMany(SeedlingRequest::class, 'barangay', 'name');
    }

    public function fishrApplications(): HasMany
    {
        return $this->hasMany(FishrApplication::class, 'barangay', 'name');
    }

    public function boatrApplications(): HasMany
    {
        return $this->hasMany(BoatrApplication::class, 'barangay', 'name');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
