<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class SeedlingRequest extends Model
{
    use HasFactory;
    
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
        'vegetables',
        'fruits',
        'fertilizers',
        'requested_quantity',
        'total_quantity',
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
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime', 
        'rejected_at' => 'datetime',
        'preferred_delivery_date' => 'date',
        'requested_quantity' => 'integer',
        'approved_quantity' => 'integer',
        'total_quantity' => 'integer',
        'vegetables' => 'array',
        'fruits' => 'array',
        'fertilizers' => 'array'
    ];

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name);
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

    public function getFormattedSeedlingsAttribute(): string
    {
        $parts = [];
        
        if (!empty($this->vegetables)) {
            $vegNames = collect($this->vegetables)->pluck('name')->toArray();
            $parts[] = 'Vegetables: ' . implode(', ', $vegNames);
        }
        
        if (!empty($this->fruits)) {
            $fruitNames = collect($this->fruits)->pluck('name')->toArray();
            $parts[] = 'Fruits: ' . implode(', ', $fruitNames);
        }
        
        if (!empty($this->fertilizers)) {
            $fertNames = collect($this->fertilizers)->pluck('name')->toArray();
            $parts[] = 'Fertilizers: ' . implode(', ', $fertNames);
        }
        
        return implode(' | ', $parts);
    }

    // Add individual formatters for each category
    public function getFormattedVegetablesAttribute(): string
    {
        if (empty($this->vegetables)) {
            return '';
        }
        
        return collect($this->vegetables)->map(function($item) {
            return $item['name'] . ' (' . $item['quantity'] . ' pcs)';
        })->implode(', ');
    }

    public function getFormattedFruitsAttribute(): string
    {
        if (empty($this->fruits)) {
            return '';
        }
        
        return collect($this->fruits)->map(function($item) {
            return $item['name'] . ' (' . $item['quantity'] . ' pcs)';
        })->implode(', ');
    }

    public function getFormattedFertilizersAttribute(): string
    {
        if (empty($this->fertilizers)) {
            return '';
        }
        
        return collect($this->fertilizers)->map(function($item) {
            return $item['name'] . ' (' . $item['quantity'] . ' pcs)';
        })->implode(', ');
    }

    // Check if request has supporting documents
    public function hasDocuments(): bool
    {
        return !empty($this->document_path) && Storage::disk('public')->exists($this->document_path);
    }

    // Get document URL
    public function getDocumentUrlAttribute(): ?string
    {
        if ($this->hasDocuments()) {
            return Storage::disk('public')->url($this->document_path);
        }
        
        return null;
    }
}