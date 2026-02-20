<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecycleBin extends Model
{
    protected $table = 'recycle_bins';

    protected $fillable = [
        'model_type',
        'model_id',
        'data',
        'deleted_by',
        'reason',
        'item_name',
        'deleted_at',
        'restored_at',
        'restored_by',
    ];

    protected $casts = [
        'data' => 'array',
        'deleted_at' => 'datetime',
        'restored_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====

    /**
     * Get the user who deleted this item
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Get the user who restored this item
     */
    public function restoredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'restored_by');
    }

    // ===== ACCESSORS =====

    /**
     * Get formatted item name with type
     */
    public function getDisplayNameAttribute(): string
    {
        $modelType = class_basename($this->model_type);
        return "{$this->item_name} ({$modelType})";
    }

    // ===== SCOPES =====

    /**
     * Scope to get only User (Admin) items
     */
    public function scopeUser($query)
    {
        return $query->where('model_type', 'App\Models\User');
    }

    /**
     * Scope to get only FishR items
     */
    public function scopeFishR($query)
    {
        return $query->where('model_type', 'App\Models\FishrApplication');
    }

    /**
     * Scope to get only BoatR items
     */
    public function scopeBoatR($query)
    {
        return $query->where('model_type', 'App\Models\BoatrApplication');
    }

    /**
     * Scope to get only RSBSA items
     */
    public function scopeRsbsa($query)
    {
        return $query->where('model_type', 'App\Models\RsbsaApplication');
    }

    /**
     * Scope to get non-restored items
     */
    public function scopeNotRestored($query)
    {
        return $query->whereNull('restored_at');
    }

    /**
     * Scope to get only Seedling Request items
     */
    public function scopeSeedlingRequest($query)
    {
        return $query->where('model_type', 'App\Models\SeedlingRequest');
    }

    /**
     * Scope to get only Training items
     */
    public function scopeTraining($query)
    {
        return $query->where('model_type', 'App\Models\TrainingApplication');
    }

    /**
     * Scope to get only CategoryItem items
     */
    public function scopeCategoryItem($query)
    {
        return $query->where('model_type', 'App\Models\CategoryItem');
    }

    /**
     * Scope to get only RequestCategory items
     */
    public function scopeRequestCategory($query)
    {
        return $query->where('model_type', 'App\Models\RequestCategory');
    }

    /**
     * Scope to get only BoatR Annex items
     */
    public function scopeBoatrAnnex($query)
    {
        return $query->where('model_type', 'App\Models\BoatrAnnex');
    }

    /**
     * Scope to get only FishR Annex items
     */
    public function scopeFishrAnnex($query)
    {
        return $query->where('model_type', 'App\Models\FishrAnnex');
    }

    /**
     * Get a human-readable type name
     */
    public function getTypeNameAttribute(): string
    {
        return match ($this->model_type) {
            'App\Models\User' => 'Admin User',
            'App\Models\FishrApplication' => 'FishR Registration',
            'App\Models\FishrAnnex' => 'FishR Annex',
            'App\Models\BoatrAnnex' => 'BoatR Annex',
            'App\Models\BoatrApplication' => 'BoatR Registration',
            'App\Models\RsbsaApplication' => 'RSBSA Registration',
            'App\Models\SeedlingRequest' => 'Supply Request',
            'App\Models\CategoryItem' => 'Supply Item',           
            'App\Models\RequestCategory' => 'Supply Category',   
            'App\Models\TrainingApplication' => 'Training Request',
            'App\Models\UserRegistration' => 'User Registration',
            default => 'Unknown Item'
        };
    }
}