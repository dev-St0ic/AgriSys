<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Models\Activity;

class ActivityLogView extends Model
{
    use HasFactory;

    protected $table = 'activity_log_views';

    protected $fillable = [
        'activity_id',
        'user_id',
        'ip_address',
        'user_agent',
        'browser',
        'os',
        'location',
        'session_id',
        'viewed_at',
        'view_ended_at',
        'purpose',
        'notes'
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
        'view_ended_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the activity that was viewed
     */
    public function activity()
    {
        return $this->belongsTo(Activity::class, 'activity_id');
    }

    /**
     * Get the user who viewed the activity
     */
    public function viewer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get view duration in seconds
     */
    public function getViewDurationAttribute(): ?int
    {
        if ($this->view_ended_at) {
            return $this->view_ended_at->diffInSeconds($this->viewed_at);
        }
        return null;
    }

    /**
     * Get formatted view duration
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->view_duration) {
            return 'N/A';
        }

        if ($this->view_duration < 60) {
            return $this->view_duration . ' seconds';
        }

        $minutes = intdiv($this->view_duration, 60);
        $seconds = $this->view_duration % 60;
        
        return "{$minutes}m {$seconds}s";
    }

    /**
     * Scope for specific activity
     */
    public function scopeForActivity($query, $activityId)
    {
        return $query->where('activity_id', $activityId);
    }

    /**
     * Scope for specific user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('viewed_at', [$from, $to]);
    }

    /**
     * Scope for specific IP
     */
    public function scopeFromIp($query, $ip)
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Scope for incomplete views (still viewing)
     */
    public function scopeIncomplete($query)
    {
        return $query->whereNull('view_ended_at');
    }

    /**
     * Scope for suspicious activity (multiple views from different IPs)
     */
    public function scopeSuspicious($query)
    {
        return $query->groupBy('activity_id')
            ->havingRaw('COUNT(DISTINCT ip_address) > 3');
    }

    /**
     * Check if view is from a different IP than the viewer's usual location
     */
    public function isAnomalousAccess(): bool
    {
        // Get user's most common IP
        $commonIp = self::where('user_id', $this->user_id)
            ->groupBy('ip_address')
            ->selectRaw('ip_address, COUNT(*) as count')
            ->orderBy('count', 'desc')
            ->first();

        if (!$commonIp) {
            return false;
        }

        return $this->ip_address !== $commonIp->ip_address;
    }

    /**
     * Log view ended
     */
    public function endView()
    {
        $this->update(['view_ended_at' => now()]);
    }
}