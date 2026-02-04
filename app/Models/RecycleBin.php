<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

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
        'expires_at'
    ];

    protected $casts = [
        'data' => 'array',
        'deleted_at' => 'datetime',
        'restored_at' => 'datetime',
        'expires_at' => 'datetime',
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
     * Get days until permanent deletion
     */
    public function getDaysUntilExpireAttribute(): int
    {
        if (!$this->expires_at) {
            return 0;
        }
        return max(0, $this->expires_at->diffInDays(now()));
    }

    /**
     * Check if item is expired
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && now()->isAfter($this->expires_at);
    }

    /**
     * Get formatted deleted date
     */
    public function getFormattedDeletedAtAttribute(): string
    {
        return $this->deleted_at->format('M d, Y h:i A');
    }

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
     * Scope to get non-expired items
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Scope to get expired items
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    /**
     * Scope to get non-restored items
     */
    public function scopeNotRestored($query)
    {
        return $query->whereNull('restored_at');
    }

    // ===== METHODS =====

    /**
     * Restore the deleted item
     */
    public function restore(): bool
    {
        try {
            // Get the model class
            $modelClass = $this->model_type;
            
            // For FishR, we need to handle soft delete
            if ($modelClass === 'App\Models\FishrApplication') {
                $restored = FishrApplication::withTrashed()
                    ->find($this->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            } else {
                // For other models, recreate from data
                $model = new $modelClass();
                $model->id = $this->model_id;
                foreach ($this->data as $key => $value) {
                    $model->$key = $value;
                }
                $model->save();
            }

            // Mark as restored in recycle bin
            $this->update([
                'restored_at' => now(),
                'restored_by' => auth()->id(),
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Error restoring item from recycle bin', [
                'model_type' => $this->model_type,
                'model_id' => $this->model_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

 public function permanentlyDelete(): bool
{
    try {
        $modelClass = $this->model_type;
        if ($modelClass === 'App\Models\FishrApplication') {
            FishrApplication::withTrashed()
                ->where('id', $this->model_id)
                ->forceDelete();
        } 
        elseif ($modelClass === 'App\Models\SeedlingRequest') {
            SeedlingRequest::withTrashed()
                ->where('id', $this->model_id)
                ->forceDelete();
        }

        $this->forceDelete();

        return true;
    } catch (\Exception $e) {
        \Log::error('Error permanently deleting item from recycle bin', [
            'model_type' => $this->model_type,
            'model_id' => $this->model_id,
            'error' => $e->getMessage()
        ]);
        return false;
    }
}

    /**
 * Get a human-readable type name
 */
public function getTypeNameAttribute(): string
{
    return match ($this->model_type) {
        'App\Models\FishrApplication' => 'FishR Registration',
        'App\Models\FishrAnnex' => 'FishR Annex',
        'App\Models\BoatrAnnex' => 'BoatR Annex',
        'App\Models\BoatrApplication' => 'BoatR Registration',
        'App\Models\RsbsaApplication' => 'RSBSA Application',
        'App\Models\SeedlingRequest' => 'Supply Request',
        'App\Models\CategoryItem' => 'Supply Item',           
        'App\Models\RequestCategory' => 'Supply Category',   
        'App\Models\TrainingApplication' => 'Training Request',
        default => 'Unknown Item'
    };
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


}