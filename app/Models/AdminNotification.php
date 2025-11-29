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
        return match($this->type) {
            'seedling_request_new' => 'fa-seedling',
            'seedling_request_approved' => 'fa-check-circle',
            'seedling_request_rejected' => 'fa-times-circle',
            'seedling_request_updated' => 'fa-edit',
            'seedling_stock_low' => 'fa-exclamation-triangle',
            'seedling_stock_out' => 'fa-box-open',
            default => 'fa-bell'
        };
    }

    public function getColorAttribute(): string
    {
        return match($this->type) {
            'seedling_request_new' => 'primary',
            'seedling_request_approved' => 'success',
            'seedling_request_rejected' => 'danger',
            'seedling_request_updated' => 'info',
            'seedling_stock_low' => 'warning',
            'seedling_stock_out' => 'danger',
            default => 'secondary'
        };
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public static function notifyAdmins(string $type, string $title, string $message, ?array $data = null, ?string $actionUrl = null)
    {
        $admins = User::where('role', 'admin')->orWhere('role', 'super_admin')->get();

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