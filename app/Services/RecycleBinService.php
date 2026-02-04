<?php

namespace App\Services;

use App\Models\RecycleBin;
use App\Models\FishrApplication;
use App\Models\FishrAnnex;
use App\Models\SeedlingRequest;
use App\Models\CategoryItem;
use App\Models\RequestCategory;
use App\Models\TrainingApplication;
use App\Models\UserRegistration;
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
            
            // Create recycle bin entry with timestamps
            RecycleBin::create([
                'model_type' => $modelClass,
                'model_id' => $model->id,
                'item_name' => $model->full_name ?? $model->name ?? $model->title ?? 'Unknown Item',
                'reason' => $reason,
                'data' => $model->toArray(),
                'deleted_by' => auth()->id(),
                'deleted_at' => now(),
                'expires_at' => now()->addDays(30), // Auto-delete after 30 days
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
     * FIXED: Restore item from recycle bin
     * Comprehensive handling for all model types
     */
    public static function restore($item)
    {
        try {
            $modelClass = $item->model_type;
            
            // Handle FishrApplication with soft delete and its annexes
            if ($modelClass === 'App\Models\FishrApplication') {
                $restored = FishrApplication::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                    
                    // Restore all soft-deleted annexes
                    if (method_exists($restored, 'annexes')) {
                        $restored->annexes()->withTrashed()->restore();
                    }
                }
            }
            
            // Handle FishrAnnex with soft delete
            elseif ($modelClass === 'App\Models\FishrAnnex') {
                $restored = FishrAnnex::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
            
            // Handle SeedlingRequest with soft delete
            elseif ($modelClass === 'App\Models\SeedlingRequest') {
                $restored = SeedlingRequest::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
            
            // Handle CategoryItem with soft delete
            elseif ($modelClass === 'App\Models\CategoryItem') {
                $restored = CategoryItem::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
            
            // Handle RequestCategory with soft delete
            elseif ($modelClass === 'App\Models\RequestCategory') {
                $restored = RequestCategory::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
            
            // Handle UserRegistration with soft delete
            elseif ($modelClass === 'App\Models\UserRegistration') {
                $restored = UserRegistration::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
            
            // Handle BoatrApplication with soft delete
            elseif ($modelClass === 'App\Models\BoatrApplication') {
                $restored = \App\Models\BoatrApplication::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
            
            // Handle RsbsaApplication with soft delete
            elseif ($modelClass === 'App\Models\RsbsaApplication') {
                $restored = \App\Models\RsbsaApplication::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
            
            // Handle TrainingApplication with soft delete
            elseif ($modelClass === 'App\Models\TrainingApplication') {
                $restored = TrainingApplication::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
            
            // Fallback: Try to recreate from stored data
            else {
                try {
                    $model = new $modelClass();
                    $model->id = $item->model_id;
                    foreach ($item->data as $key => $value) {
                        $model->$key = $value;
                    }
                    $model->save();
                } catch (\Exception $fallbackError) {
                    Log::warning('Could not restore model using fallback method', [
                        'model_type' => $modelClass,
                        'model_id' => $item->model_id,
                        'error' => $fallbackError->getMessage()
                    ]);
                    return false;
                }
            }

            // Mark as restored in recycle bin
            $item->update([
                'restored_at' => now(),
                'restored_by' => auth()->id(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error in RecycleBinService::restore', [
                'model_type' => $item->model_type,
                'model_id' => $item->model_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * FIXED: Permanently delete item from recycle bin
     * Comprehensive handling for all model types with file cleanup
     */
    public static function permanentlyDelete($item)
    {
        try {
            $modelClass = $item->model_type;
            
            // Handle FishrApplication - delete annexes, documents, then application
            if ($modelClass === 'App\Models\FishrApplication') {
                $registration = FishrApplication::withTrashed()
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
            
            // Handle FishrAnnex - delete file then annex
            elseif ($modelClass === 'App\Models\FishrAnnex') {
                $annex = FishrAnnex::withTrashed()
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
            
            // Handle TrainingApplication - delete document then application
            elseif ($modelClass === 'App\Models\TrainingApplication') {
                if ($item->data['document_path'] ?? null) {
                    if (Storage::disk('public')->exists($item->data['document_path'])) {
                        Storage::disk('public')->delete($item->data['document_path']);
                    }
                }
                
                TrainingApplication::withTrashed()
                    ->where('id', $item->model_id)
                    ->forceDelete();
            }
            
            // Handle SeedlingRequest
            elseif ($modelClass === 'App\Models\SeedlingRequest') {
                SeedlingRequest::withTrashed()
                    ->where('id', $item->model_id)
                    ->forceDelete();
            }
            
            // Handle UserRegistration - delete documents then user registration
            elseif ($modelClass === 'App\Models\UserRegistration') {
                // Delete uploaded documents
                if ($item->data['id_front_path'] ?? null) {
                    if (Storage::disk('public')->exists($item->data['id_front_path'])) {
                        Storage::disk('public')->delete($item->data['id_front_path']);
                    }
                }
                if ($item->data['id_back_path'] ?? null) {
                    if (Storage::disk('public')->exists($item->data['id_back_path'])) {
                        Storage::disk('public')->delete($item->data['id_back_path']);
                    }
                }
                if ($item->data['location_document_path'] ?? null) {
                    if (Storage::disk('public')->exists($item->data['location_document_path'])) {
                        Storage::disk('public')->delete($item->data['location_document_path']);
                    }
                }
                if ($item->data['profile_image_url'] ?? null) {
                    if (Storage::disk('public')->exists($item->data['profile_image_url'])) {
                        Storage::disk('public')->delete($item->data['profile_image_url']);
                    }
                }
                
                UserRegistration::withTrashed()
                    ->where('id', $item->model_id)
                    ->forceDelete();
            }
            
            // Handle CategoryItem
            elseif ($modelClass === 'App\Models\CategoryItem') {
                CategoryItem::withTrashed()
                    ->where('id', $item->model_id)
                    ->forceDelete();
            }
            
            // Handle RequestCategory
            elseif ($modelClass === 'App\Models\RequestCategory') {
                RequestCategory::withTrashed()
                    ->where('id', $item->model_id)
                    ->forceDelete();
            }
            
            // Handle BoatrApplication
            elseif ($modelClass === 'App\Models\BoatrApplication') {
                \App\Models\BoatrApplication::withTrashed()
                    ->where('id', $item->model_id)
                    ->forceDelete();
            }
            
            // Handle RsbsaApplication
            elseif ($modelClass === 'App\Models\RsbsaApplication') {
                \App\Models\RsbsaApplication::withTrashed()
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
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Get recycle bin statistics
     * FIXED: Now returns fishr_annex_items in the array
     */
    public static function getStats()
    {
        $total = RecycleBin::notRestored()->count();
        $fishr = RecycleBin::notRestored()->where('model_type', 'App\Models\FishrApplication')->count();
        $boatr = RecycleBin::notRestored()->where('model_type', 'App\Models\BoatrApplication')->count();
        $fishrAnnex = RecycleBin::notRestored()->where('model_type', 'App\Models\FishrAnnex')->count();
        $expired = RecycleBin::notRestored()->where('expires_at', '<=', now())->count();
        $supplyCategories = RecycleBin::notRestored()->where('model_type', 'App\Models\CategoryItem')->count();
        $supplyItems = RecycleBin::notRestored()->where('model_type', 'App\Models\RequestCategory')->count();

        return [
            'total_items' => $total,
            'fishr_items' => $fishr,
            'boatr_items' => $boatr,
            'fishr_annex_items' => $fishrAnnex,  // ← THIS WAS MISSING!
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