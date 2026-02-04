<?php

namespace App\Services;

use App\Models\RecycleBin;
use App\Models\FishrApplication;
use App\Models\FishrAnnex;
use App\Models\BoatrApplication;  
use App\Models\BoatrAnnex;         
use App\Models\RsbsaApplication;  
use App\Models\SeedlingRequest;
use App\Models\CategoryItem;
use App\Models\RequestCategory;
use App\Models\TrainingApplication;
use App\Models\UserRegistration;
use Illuminate\Support\Facades\Log;

class RecycleBinService
{
    /**
     * Soft delete model - move to recycle bin
     */
    public static function softDelete($model, $reason = null)
    {
        try {
            $modelClass = get_class($model);
            
            RecycleBin::create([
                'model_type' => $modelClass,
                'model_id' => $model->id,
                'item_name' => $model->full_name ?? $model->name ?? $model->title ?? 'Unknown Item',
                'reason' => $reason,
                'data' => $model->toArray(),
                'deleted_by' => auth()->id(),
                'deleted_at' => now()
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
     * Restore item from recycle bin
     */
    public static function restore($item)
    {
        try {
            $modelClass = $item->model_type;
            
            if ($modelClass === 'App\Models\FishrApplication') {
                $restored = FishrApplication::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                    
                    if (method_exists($restored, 'annexes')) {
                        $restored->annexes()->withTrashed()->restore();
                    }
                }
            }
            elseif ($modelClass === 'App\Models\FishrAnnex') {
                $restored = FishrAnnex::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
            elseif ($modelClass === 'App\Models\SeedlingRequest') {
                $restored = SeedlingRequest::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                    $restored->items()->withTrashed()->restore();
                }
            }
            elseif ($modelClass === 'App\Models\CategoryItem') {
                $restored = CategoryItem::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
            elseif ($modelClass === 'App\Models\RequestCategory') {
                $restored = RequestCategory::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                    
                    if (method_exists($restored, 'items')) {
                        $restored->items()->withTrashed()->restore();
                    }
                }
            }
            elseif ($modelClass === 'App\Models\UserRegistration') {
                $restored = UserRegistration::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
            elseif ($modelClass === 'App\Models\BoatrApplication') {
                $restored = BoatrApplication::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                    
                    if (method_exists($restored, 'annexes')) {
                        $restored->annexes()->withTrashed()->restore();
                    }
                }
            }
            elseif ($modelClass === 'App\Models\BoatrAnnex') {
                $restored = BoatrAnnex::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
            elseif ($modelClass === 'App\Models\RsbsaApplication') {
                $restored = RsbsaApplication::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
            elseif ($modelClass === 'App\Models\TrainingApplication') {
                $restored = TrainingApplication::withTrashed()
                    ->find($item->model_id);
                
                if ($restored) {
                    $restored->restore();
                }
            }
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
        $fishrAnnex = RecycleBin::notRestored()->where('model_type', 'App\Models\FishrAnnex')->count();
        $boatrAnnex = RecycleBin::notRestored()->where('model_type', 'App\Models\BoatrAnnex')->count();
        $supplyCategories = RecycleBin::notRestored()->where('model_type', 'App\Models\CategoryItem')->count();
        $supplyItems = RecycleBin::notRestored()->where('model_type', 'App\Models\RequestCategory')->count();

        return [
            'total_items' => $total,
            'fishr_items' => $fishr,
            'boatr_items' => $boatr,
            'boatr_annex_items' => $boatrAnnex,
            'fishr_annex_items' => $fishrAnnex, 
            'supply_category_items' => $supplyCategories, 
            'supply_item_items' => $supplyItems, 
        ];
    }
}