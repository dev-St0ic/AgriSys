<?php

namespace App\Services;

use App\Models\RecycleBin;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RecycleBinService
{
    /**
     * Soft delete model - move to recycle bin
     */
 public static function softDelete($model, $reason = null)
{
    try {
        // Get the model class name
        $modelClass = get_class($model);
        
        // ✅ GET THE ITEM NAME FIRST
        $itemName = self::getItemName($model, $modelClass);
        
        // Create recycle bin entry with timestamps
        RecycleBin::create([
            'model_type' => $modelClass,
            'model_id' => $model->id,
            'item_name' => $itemName,  // ✅ NOW DEFINED
            'reason' => $reason,
            'data' => $model->toArray(),
            'deleted_by' => auth()->id(),
            'deleted_at' => now(),
            'expires_at' => now()->addDays(30),
        ]);

        // Soft delete the model
        $model->delete();

        return true;
    } catch (\Exception $e) {
        Log::error('Error in RecycleBinService::softDelete', [
            'error' => $e->getMessage()
        ]);
        return false;
    }
}

        /**
     * Get appropriate item name based on model type
     * ✅ NEW: Handles different models correctly
     */
    private static function getItemName($model, $modelClass): string
    {
        return match ($modelClass) {
            'App\Models\FishrApplication' => $model->full_name ?? $model->name ?? 'Unknown FishR',
            'App\Models\FishrAnnex' => "Annex - {$model->title}" ?? 'Unknown Annex',
            'App\Models\BoatrApplication' => $model->full_name ?? $model->name ?? 'Unknown BoatR',
            'App\Models\RsbsaApplication' => $model->full_name ?? $model->name ?? 'Unknown RSBSA',
            'App\Models\UserRegistration' => $model->full_name ?? $model->name ?? 'Unknown User',
            'App\Models\SeedlingRequest' => $model->name ?? 'Unknown Seedling Request',
            'App\Models\TrainingApplication' => $model->title ?? $model->name ?? 'Unknown Training',
            'App\Models\CategoryItem' => $model->name ?? $model->title ?? 'Unknown Category Item',
            'App\Models\RequestCategory' => $model->name ?? $model->title ?? 'Unknown Request Category',
            default => $model->name ?? $model->title ?? $model->full_name ?? 'Unknown Item'
        };
    }

    /**
     * Restore item from recycle bin
     */
    public static function restore($item)
    {
        try {
            $modelClass = $item->model_type;
            
            if ($modelClass === 'App\Models\FishrApplication') {
                $restored = \App\Models\FishrApplication::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                    
                    // Restore all soft-deleted annexes
                    if (method_exists($restored, 'annexes')) {
                        $restored->annexes()->withTrashed()->restore();
                    }
                }
            }
            elseif ($modelClass === 'App\Models\FishrAnnex') {
                $restored = \App\Models\FishrAnnex::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
            elseif ($modelClass === 'App\Models\SeedlingRequest') {
                $restored = \App\Models\SeedlingRequest::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
            elseif ($modelClass === 'App\Models\CategoryItem') {
                $restored = \App\Models\CategoryItem::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
            elseif ($modelClass === 'App\Models\RequestCategory') {
                $restored = \App\Models\RequestCategory::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
            elseif ($modelClass === 'App\Models\UserRegistration') {
                $restored = \App\Models\UserRegistration::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
            else {
                // Recreate from stored data
                $model = new $modelClass();
                $model->id = $item->model_id;
                foreach ($item->data as $key => $value) {
                    $model->$key = $value;
                }
                $model->save();
            }

            // Mark as restored
            $item->update([
                'restored_at' => now(),
                'restored_by' => auth()->id(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error in RecycleBinService::restore', [
                'model_type' => $item->model_type,
                'model_id' => $item->model_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Permanently delete item from recycle bin
     */
    public static function permanentlyDelete($item)
    {
        try {
            $modelClass = $item->model_type;
            
            if ($modelClass === 'App\Models\FishrApplication') {
                $registration = \App\Models\FishrApplication::withTrashed()
                    ->find($item->model_id);
                
                if ($registration) {
                    // Delete all annexes
                    $annexes = $registration->annexes()->withTrashed()->get();
                    foreach ($annexes as $annex) {
                        if ($annex->file_path && Storage::disk('public')->exists($annex->file_path)) {
                            Storage::disk('public')->delete($annex->file_path);
                        }
                        $annex->forceDelete();
                    }
                    
                    // Delete main document
                    if ($registration->document_path && Storage::disk('public')->exists($registration->document_path)) {
                        Storage::disk('public')->delete($registration->document_path);
                    }
                    
                    // Force delete registration
                    $registration->forceDelete();
                }
            }
            elseif ($modelClass === 'App\Models\FishrAnnex') {
                $annex = \App\Models\FishrAnnex::withTrashed()
                    ->find($item->model_id);
                
                if ($annex) {
                    // Delete file
                    if ($annex->file_path && Storage::disk('public')->exists($annex->file_path)) {
                        Storage::disk('public')->delete($annex->file_path);
                    }
                    
                    // Force delete annex
                    $annex->forceDelete();
                }
            }
            elseif ($modelClass === 'App\Models\TrainingApplication') {
                if ($item->data['document_path'] ?? null) {
                    Storage::disk('public')->delete($item->data['document_path']);
                }
                
                \App\Models\TrainingApplication::withTrashed()
                    ->where('id', $item->model_id)
                    ->forceDelete();
            }
            elseif ($modelClass === 'App\Models\SeedlingRequest') {
                \App\Models\SeedlingRequest::withTrashed()
                    ->where('id', $item->model_id)
                    ->forceDelete();
            }
            elseif ($modelClass === 'App\Models\UserRegistration') {
                // Delete uploaded documents
                if ($item->data['id_front_path'] ?? null) {
                    Storage::disk('public')->delete($item->data['id_front_path']);
                }
                if ($item->data['id_back_path'] ?? null) {
                    Storage::disk('public')->delete($item->data['id_back_path']);
                }
                if ($item->data['location_document_path'] ?? null) {
                    Storage::disk('public')->delete($item->data['location_document_path']);
                }
                if ($item->data['profile_image_url'] ?? null) {
                    Storage::disk('public')->delete($item->data['profile_image_url']);
                }
                
                \App\Models\UserRegistration::withTrashed()
                    ->where('id', $item->model_id)
                    ->forceDelete();
            }
            elseif ($modelClass === 'App\Models\CategoryItem') {
                \App\Models\CategoryItem::withTrashed()
                    ->where('id', $item->model_id)
                    ->forceDelete();
            }
            elseif ($modelClass === 'App\Models\RequestCategory') {
                \App\Models\RequestCategory::withTrashed()
                    ->where('id', $item->model_id)
                    ->forceDelete();
            }

            // Delete recycle bin entry
            $item->forceDelete();

            return true;
        } catch (\Exception $e) {
            Log::error('Error in RecycleBinService::permanentlyDelete', [
                'model_type' => $item->model_type,
                'model_id' => $item->model_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Get recycle bin statistics
     */
    public static function getStats()
    {
        $total = RecycleBin::notRestored()->count();
        $fishr = RecycleBin::notRestored()->where('model_type', 'App\Models\FishrApplication')->count();
        $boatr = RecycleBin::notRestored()->where('model_type', 'App\Models\BoatrApplication')->count();
        $expired = RecycleBin::notRestored()->where('expires_at', '<=', now())->count();
        $supplyCategories = RecycleBin::notRestored()->where('model_type', 'App\Models\CategoryItem')->count();
        $supplyItems = RecycleBin::notRestored()->where('model_type', 'App\Models\RequestCategory')->count();

        return [
            'total_items' => $total,
            'fishr_items' => $fishr,
            'boatr_items' => $boatr,
            'expired_items' => $expired,
            'supply_category_items' => $supplyCategories,
            'supply_item_items' => $supplyItems,
        ];
    }

    /**
     * Empty expired items
     */
    public static function emptyExpired()
    {
        try {
            $expired = RecycleBin::notRestored()->where('expires_at', '<=', now())->get();
            $count = 0;

            foreach ($expired as $item) {
                if (self::permanentlyDelete($item)) {
                    $count++;
                }
            }

            return $count;
        } catch (\Exception $e) {
            Log::error('Error in RecycleBinService::emptyExpired', [
                'error' => $e->getMessage()
            ]);
            return 0;
        }
    }
}