<?php
// app/Models/RetentionSchedule.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RetentionSchedule extends Model
{
    protected $fillable = [
        'record_type',
        'record_label',
        'retention_category',
        'retention_years',
        'legal_basis',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function forType(string $recordType): ?self
    {
        return static::where('record_type', $recordType)->where('is_active', true)->first();
    }

    public function calculateExpiryDate(): ?string
    {
        if ($this->retention_category === Archive::RETENTION_PERMANENT) {
            return null; // Never expires
        }
        return now()->addYears($this->retention_years)->toDateString();
    }
}