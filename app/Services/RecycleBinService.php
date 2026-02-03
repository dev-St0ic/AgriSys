<?php

namespace App\Services;

use App\Models\RecycleBin;
use App\Models\FishrApplication;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class RecycleBinService
{
    /**
     * Move an item to recycle bin instead of permanently deleting
     */
    public static function softDelete($model, $reason = null)
    {
        try {
            $modelClass = get_class($model);
            
            // Store item in recycle bin
            RecycleBin::create([
                'model_type' => $modelClass,
                'model_id' => $model->id,
                'data' => $model->toArray(),
                'deleted_by' => auth()->id(),
                'reason' => $reason,
                'item_name' => $model->full_name ?? $model->name ?? "Item #{$model->id}",
                'deleted_at' => now(),
                'expires_at' => now()->addDays(30), // Auto-delete after 30 days
            ]);

            // Soft delete the actual model if it supports it
            if (method_exists($model, 'delete')) {
                $model->delete();
            }

            Log::info('Item moved to recycle bin', [
                'model_type' => $modelClass,
                'model_id' => $model->id,
                'deleted_by' => auth()->id()
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error moving item to recycle bin', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Restore an item from recycle bin
     */
    public static function restore(RecycleBin $recycleBin)
    {
        try {
            if ($recycleBin->restore()) {
                Log::info('Item restored from recycle bin', [
                    'model_type' => $recycleBin->model_type,
                    'model_id' => $recycleBin->model_id,
                    'restored_by' => auth()->id()
                ]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Error restoring item from recycle bin', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Permanently delete an item from recycle bin
     */
    public static function permanentlyDelete(RecycleBin $recycleBin)
    {
        try {
            if ($recycleBin->permanentlyDelete()) {
                Log::info('Item permanently deleted from recycle bin', [
                    'model_type' => $recycleBin->model_type,
                    'model_id' => $recycleBin->model_id,
                ]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            Log::error('Error permanently deleting item from recycle bin', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Empty recycle bin (delete expired items)
     */
    public static function emptyExpired()
    {
        try {
            $expired = RecycleBin::expired()->get();
            
            foreach ($expired as $item) {
                $item->permanentlyDelete();
            }

            Log::info('Expired recycle bin items deleted', [
                'count' => $expired->count()
            ]);

            return $expired->count();
        } catch (\Exception $e) {
            Log::error('Error emptying recycle bin', [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }

    /**
     * Get recycle bin statistics
     */
    public static function getStats()
    {
        return [
            'total_items' => RecycleBin::notRestored()->count(),
            'fishr_items' => RecycleBin::fishR()->notRestored()->count(),
            'boatr_items' => RecycleBin::boatR()->notRestored()->count(),
            'rsbsa_items' => RecycleBin::rsbsa()->notRestored()->count(),
            'expired_items' => RecycleBin::expired()->notRestored()->count(),
        ];
    }
}