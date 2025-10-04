<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemSupplyLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_item_id',
        'transaction_type',
        'quantity',
        'old_supply',
        'new_supply',
        'performed_by',
        'notes',
        'source',
        'reference_type',
        'reference_id'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'old_supply' => 'integer',
        'new_supply' => 'integer',
        'performed_by' => 'integer',
        'reference_id' => 'integer'
    ];

    /**
     * Transaction types
     */
    const TYPE_RECEIVED = 'received';          // New supplies received
    const TYPE_DISTRIBUTED = 'distributed';    // Supplies given to approved applicants
    const TYPE_ADJUSTMENT = 'adjustment';      // Manual supply adjustment
    const TYPE_RETURNED = 'returned';          // Returned from cancelled/rejected requests
    const TYPE_LOSS = 'loss';                  // Expired/damaged/lost
    const TYPE_INITIAL = 'initial_supply';     // Initial supply setup

    /**
     * Get the item this log belongs to
     */
    public function categoryItem()
    {
        return $this->belongsTo(CategoryItem::class, 'category_item_id');
    }

    /**
     * Get the user who performed this transaction
     */
    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    /**
     * Get polymorphic reference (e.g., SeedlingRequest)
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * Get transaction type color
     */
    public function getTypeColorAttribute(): string
    {
        return match($this->transaction_type) {
            'received' => 'success',
            'distributed' => 'primary',
            'returned' => 'info',
            'adjustment' => 'warning',
            'loss' => 'danger',
            'initial_supply' => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Get transaction type icon
     */
    public function getTypeIconAttribute(): string
    {
        return match($this->transaction_type) {
            'received' => 'fa-arrow-up',
            'distributed' => 'fa-hand-holding',
            'returned' => 'fa-undo',
            'adjustment' => 'fa-edit',
            'loss' => 'fa-exclamation-triangle',
            'initial_supply' => 'fa-plus-circle',
            default => 'fa-circle'
        };
    }

    /**
     * Get formatted transaction type
     */
    public function getFormattedTypeAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->transaction_type));
    }

    /**
     * Get change indicator (+ or -)
     */
    public function getChangeIndicatorAttribute(): string
    {
        $increase = ['received', 'returned', 'initial_supply'];
        $decrease = ['distributed', 'loss'];
        
        if (in_array($this->transaction_type, $increase) && $this->new_supply > $this->old_supply) {
            return '+';
        }
        if (in_array($this->transaction_type, $decrease) && $this->new_supply < $this->old_supply) {
            return '-';
        }
        
        return $this->new_supply > $this->old_supply ? '+' : '-';
    }

    /**
     * Scope for specific item
     */
    public function scopeForItem($query, int $itemId)
    {
        return $query->where('category_item_id', $itemId);
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    /**
     * Scope for user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('performed_by', $userId);
    }
}