<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BoatrAnnex extends Model
{ 
    use LogsActivity, SoftDeletes;
    
    protected $table = 'boatr_annexes';

    protected $fillable = [
        'boatr_application_id',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_extension',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the BoatR application that owns the annex
     */
    public function boatrApplication(): BelongsTo
    {
        return $this->belongsTo(BoatrApplication::class, 'boatr_application_id');
    }

    /**
     * Get the user who uploaded the annex
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the file URL
     */
    public function getFileUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get human readable file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if file is an image
     */
    public function getIsImageAttribute(): bool
    {
        return in_array(strtolower($this->file_extension), ['jpg', 'jpeg', 'png', 'gif']);
    }

    /**
     * Check if file is a PDF
     */
    public function getIsPdfAttribute(): bool
    {
        return strtolower($this->file_extension) === 'pdf';
    }

    /**
     * Delete the annex and its file
     */
    public function deleteWithFile(): bool
    {
        // Delete the physical file
        if (Storage::disk('public')->exists($this->file_path)) {
            Storage::disk('public')->delete($this->file_path);
        }

        // Delete the database record
        return $this->delete();
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