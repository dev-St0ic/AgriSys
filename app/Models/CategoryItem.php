<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CategoryItem extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'unit',
        'min_quantity',
        'max_quantity',
        'image_path',
        'is_active',
        'display_order',
        // Supply management fields
        'current_supply',
        'minimum_supply',
        'maximum_supply',
        'last_supplied_at',
        'last_supplied_by',
        'reorder_point',
        'supply_alert_enabled'
    ];

    protected $casts = [
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'is_active' => 'boolean',
        'display_order' => 'integer',
        'current_supply' => 'integer',
        'minimum_supply' => 'integer',
        'maximum_supply' => 'integer',
        'reorder_point' => 'integer',
        'supply_alert_enabled' => 'boolean',
        'last_supplied_at' => 'datetime'
    ];

    /**
     * Get the category this item belongs to
     */
    public function category()
    {
        return $this->belongsTo(RequestCategory::class, 'category_id');
    }

    /**
     * Get request items
     */
    public function requestItems()
    {
        return $this->hasMany(SeedlingRequestItem::class, 'category_item_id');
    }

    /**
     * Get supply logs
     */
    public function supplyLogs()
    {
        return $this->hasMany(ItemSupplyLog::class, 'category_item_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get user who last supplied
     */
    public function lastSuppliedBy()
    {
        return $this->belongsTo(User::class, 'last_supplied_by');
    }

    /**
     * Scope for active items
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered items
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    /**
     * Scope for low supply items
     */
    public function scopeLowSupply($query)
    {
        return $query->whereNotNull('reorder_point')
            ->whereColumn('current_supply', '<=', 'reorder_point');
    }

    /**
     * Scope for out of supply items
     */
    public function scopeOutOfSupply($query)
    {
        return $query->where('current_supply', '<=', 0);
    }

    /**
     * Get formatted name with unit
     */
    public function getFormattedNameAttribute(): string
    {
        return $this->name . ' (' . $this->unit . ')';
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image_path) {
            return Storage::url($this->image_path);
        }
        return null;
    }

    /**
     * FRONTEND: Get stock status for landing page (out_of_stock, low_stock, in_stock)
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->current_supply <= 0) {
            return 'out_of_stock';
        }
        if ($this->reorder_point && $this->current_supply <= $this->reorder_point) {
            return 'low_stock';
        }
        return 'in_stock';
    }

    /**
     * ADMIN: Get supply status for admin panel (out_of_supply, low_supply, critical, adequate)
     */
    public function getSupplyStatusAttribute(): string
    {
        if ($this->current_supply <= 0) {
            return 'out_of_supply';
        }
        if ($this->reorder_point && $this->current_supply <= $this->reorder_point) {
            return 'low_supply';
        }
        if ($this->minimum_supply && $this->current_supply <= $this->minimum_supply) {
            return 'critical';
        }
        return 'adequate';
    }

    /**
     * Get supply status color
     */
    public function getSupplyStatusColorAttribute(): string
    {
        return match($this->supply_status) {
            'out_of_supply' => 'danger',
            'critical' => 'danger',
            'low_supply' => 'warning',
            'adequate' => 'success',
            default => 'secondary'
        };
    }

    /**
     * Check if item needs reordering
     */
    public function needsReorder(): bool
    {
        return $this->reorder_point && $this->current_supply <= $this->reorder_point;
    }

    /**
     * Check supply availability for distribution
     */
    public function checkSupplyAvailability(int $quantity): array
    {
        return [
            'available' => $this->current_supply >= $quantity,
            'current_supply' => $this->current_supply,
            'requested' => $quantity,
            'shortage' => max(0, $quantity - $this->current_supply),
            'message' => $this->current_supply >= $quantity 
                ? 'Supply available for distribution' 
                : "Insufficient supply. Available: {$this->current_supply}, Requested: {$quantity}"
        ];
    }

    /**
     * Add supply (receive new supplies)
     */
    public function addSupply(int $quantity, int $userId, string $notes = null, string $source = null): bool
    {
        if ($quantity <= 0) {
            return false;
        }

        $oldSupply = $this->current_supply;
        $newSupply = $oldSupply + $quantity;

        // Check maximum supply limit
        if ($this->maximum_supply && $newSupply > $this->maximum_supply) {
            return false;
        }

        $this->update([
            'current_supply' => $newSupply,
            'last_supplied_at' => now(),
            'last_supplied_by' => $userId
        ]);

        // ✅ Create log and store it in a variable
        $log = ItemSupplyLog::create([
            'category_item_id' => $this->id,
            'transaction_type' => 'received',
            'quantity' => $quantity,
            'old_supply' => $oldSupply,
            'new_supply' => $newSupply,
            'performed_by' => $userId,
            'notes' => $notes ?? "Received {$quantity} {$this->unit}",
            'source' => $source,
            'reference_type' => null,
            'reference_id' => null
        ]);

        // ✅ Notification is now handled in controller after checking stock levels
        return true;
    }

    /**
     * Distribute supply (deduct when approved for applicant)
     */
    public function distributeSupply(int $quantity, int $userId, string $notes = null, string $referenceType = null, int $referenceId = null): bool
    {
        if ($quantity <= 0 || $this->current_supply < $quantity) {
            return false;
        }

        $oldSupply = $this->current_supply;
        $newSupply = $oldSupply - $quantity;

        $this->update(['current_supply' => $newSupply]);

        // ✅ Create log and store it in a variable
        $log = ItemSupplyLog::create([
            'category_item_id' => $this->id,
            'transaction_type' => 'distributed',
            'quantity' => $quantity,
            'old_supply' => $oldSupply,
            'new_supply' => $newSupply,
            'performed_by' => $userId,
            'notes' => $notes ?? "Distributed {$quantity} {$this->unit}",
            'source' => null,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId
        ]);

        // ✅ Notification is now handled in SeedlingRequestController where stock levels are checked
        return true;
    }

    /**
     * Adjust supply (manual adjustment)
     */
    public function adjustSupply(int $newSupply, int $userId, string $reason): bool
    {
        if ($newSupply < 0) {
            return false;
        }

        // Check maximum supply limit
        if ($this->maximum_supply && $newSupply > $this->maximum_supply) {
            return false;
        }

        $oldSupply = $this->current_supply;
        $difference = $newSupply - $oldSupply;

        $this->update(['current_supply' => $newSupply]);

        // ✅ Create log and store it in a variable
        $log = ItemSupplyLog::create([
            'category_item_id' => $this->id,
            'transaction_type' => 'adjustment',
            'quantity' => abs($difference),
            'old_supply' => $oldSupply,
            'new_supply' => $newSupply,
            'performed_by' => $userId,
            'notes' => "Supply adjusted: {$reason} (Change: " . ($difference > 0 ? "+{$difference}" : $difference) . ")",
            'source' => null,
            'reference_type' => null,
            'reference_id' => null
        ]);

        // ✅ Notification is now handled in controller where stock levels are checked
        return true;
    }

    /**
     * Return supply (for cancelled/rejected distributions)
     */
    public function returnSupply(int $quantity, int $userId, string $notes = null, string $referenceType = null, int $referenceId = null): bool
    {
        if ($quantity <= 0) {
            return false;
        }

        $oldSupply = $this->current_supply;
        $newSupply = $oldSupply + $quantity;

        // Check maximum supply limit
        if ($this->maximum_supply && $newSupply > $this->maximum_supply) {
            $newSupply = $this->maximum_supply;
            $quantity = $newSupply - $oldSupply;
        }

        $this->update(['current_supply' => $newSupply]);

        // ✅ Create log and store it in a variable
        $log = ItemSupplyLog::create([
            'category_item_id' => $this->id,
            'transaction_type' => 'returned',
            'quantity' => $quantity,
            'old_supply' => $oldSupply,
            'new_supply' => $newSupply,
            'performed_by' => $userId,
            'notes' => $notes ?? "Returned {$quantity} {$this->unit} to supply",
            'source' => null,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId
        ]);

        // ✅ Notification is handled in SeedlingRequestController
        return true;
    }

    /**
     * Record expired/damaged supplies
     */
    public function recordLoss(int $quantity, int $userId, string $reason): bool
    {
        if ($quantity <= 0 || $this->current_supply < $quantity) {
            return false;
        }

        $oldSupply = $this->current_supply;
        $newSupply = $oldSupply - $quantity;

        $this->update(['current_supply' => $newSupply]);

        // ✅ Create log and store it in a variable
        $log = ItemSupplyLog::create([
            'category_item_id' => $this->id,
            'transaction_type' => 'loss',
            'quantity' => $quantity,
            'old_supply' => $oldSupply,
            'new_supply' => $newSupply,
            'performed_by' => $userId,
            'notes' => "Loss recorded: {$reason}",
            'source' => null,
            'reference_type' => null,
            'reference_id' => null
        ]);

        // ✅ Notification is now handled in controller where stock levels are checked
        return true;
    }

    // Log activity options
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}