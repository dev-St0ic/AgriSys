<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'action_url',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function markAsRead()
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }

    public function markAsUnread()
    {
        if ($this->is_read) {
            $this->update([
                'is_read' => false,
                'read_at' => null
            ]);
        }
    }

    public function getIconAttribute(): string
    {
        // All notifications show the bell icon
        return 'fa-bell';
    }

    public function getColorAttribute(): string
    {
        return match($this->type) {
            // Seedling/Supply Requests
            'seedling_request_new' => 'primary',
            'seedling_request_approved' => 'success',
            'seedling_request_rejected' => 'danger',
            'seedling_request_updated' => 'info',
            'seedling_request_document_updated' => 'info',
            'seedling_stock_low' => 'warning',
            'seedling_stock_out' => 'danger',
            'seedling_bulk_imported' => 'success',
            // Supply Management - Categories
            'supply_category_created' => 'primary',
            'supply_category_updated' => 'info',
            'supply_category_deleted' => 'danger',
            'supply_category_activated' => 'success',
            'supply_category_deactivated' => 'warning',
            // Supply Management - Items
            'supply_item_created' => 'primary',
            'supply_item_updated' => 'info',
            'supply_item_deleted' => 'danger',
            'supply_item_activated' => 'success',
            'supply_item_deactivated' => 'warning',
            // Training Applications
            'training_application_new' => 'primary',
            'training_application_approved' => 'success',
            'training_application_rejected' => 'danger',
            'training_application_updated' => 'info',
            'training_application_document_updated' => 'info',
            'training_bulk_imported' => 'success',
            default => 'secondary'
        };
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public static function notifyAdmins(string $type, string $title, string $message, ?array $data = null, ?string $actionUrl = null, ?string $category = null)
    {
        $admins = User::where('role', 'admin')->orWhere('role', 'superadmin')->get();

        foreach ($admins as $admin) {
            static::create([
                'user_id' => $admin->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => $data,
                'action_url' => $actionUrl
            ]);
        }
    }
}
