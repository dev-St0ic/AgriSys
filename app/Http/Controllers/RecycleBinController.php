<?php

namespace App\Http\Controllers;

use App\Models\RecycleBin;
use App\Models\UserRegistration;
use App\Services\RecycleBinService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RecycleBinController extends Controller
{
    /**
     * Display recycle bin items
     */
    public function index(Request $request)
    {
        try {
            $query = RecycleBin::notRestored();

            // Filter by type
            if ($request->filled('type')) {
                $typeMap = [
                    'fishr' => 'App\Models\FishrApplication',
                    'boatr' => 'App\Models\BoatrApplication',
                    'rsbsa' => 'App\Models\RsbsaApplication',
                    'seedlings' => 'App\Models\SeedlingRequest',
                    'training' => 'App\Models\TrainingApplication',
                    'user_registration' => 'App\Models\UserRegistration', // NEW
                     'category_item' => 'App\Models\CategoryItem',
                'request_category' => 'App\Models\RequestCategory',
                ];

                if (isset($typeMap[$request->type])) {
                    $query->where('model_type', $typeMap[$request->type]);
                }
            }

            // Filter by status
            if ($request->filled('status')) {
                if ($request->status === 'expired') {
                    $query->where('expires_at', '<=', now());
                } elseif ($request->status === 'active') {
                    $query->where('expires_at', '>', now());
                }
            }

            // Search
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('item_name', 'like', "%{$search}%")
                      ->orWhere('reason', 'like', "%{$search}%");
                });
            }

            // Paginate
            $items = $query->orderBy('deleted_at', 'desc')
                          ->paginate(15)
                          ->appends($request->query());

            // Get statistics
            $stats = RecycleBinService::getStats();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'data' => $items,
                    'view' => view('admin.recycle-bin.partials.table', compact('items'))->render()
                ]);
            }

            return view('admin.recycle-bin.index', compact('items', 'stats'));

        } catch (\Exception $e) {
            Log::error('Error loading recycle bin', [
                'error' => $e->getMessage()
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading recycle bin'
                ], 500);
            }

            return redirect()->back()->with('error', 'Error loading recycle bin');
        }
    }

    /**
     * View item details before restoring
     */
    public function show($id)
    {
        try {
            $item = RecycleBin::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $item->id,
                    'type_name' => $item->type_name,
                    'item_name' => $item->item_name,
                    'reason' => $item->reason,
                    'deleted_by_name' => $item->deletedBy->name ?? 'Unknown',
                    'deleted_at' => $item->formatted_deleted_at,
                    'days_until_expire' => $item->days_until_expire,
                    'is_expired' => $item->is_expired,
                    'data' => $item->data,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading recycle bin item', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Item not found'
            ], 404);
        }
    }

    /**
     * UPDATED: Restore item from recycle bin
     * Now handles UserRegistration with soft delete
     */
    public function restore(): bool
{
    try {
        $modelClass = $this->model_type;
        
        if ($modelClass === 'App\Models\FishrApplication') {
            $restored = FishrApplication::withTrashed()
                ->find($this->model_id);
            
            if ($restored) {
                // Restore the registration
                $restored->restore();
                
                // Also restore all its annexes if they're soft-deleted
                if (method_exists($restored, 'annexes')) {
                    $restored->annexes()->withTrashed()->restore();
                }
            }
        }
        elseif ($modelClass === 'App\Models\SeedlingRequest') {
            $restored = SeedlingRequest::withTrashed()
                ->find($this->model_id);
            
            if ($restored) {
                $restored->restore();
            }
        }
        elseif ($modelClass === 'App\Models\CategoryItem') {
            // ADD THIS BLOCK
            $restored = CategoryItem::withTrashed()
                ->find($this->model_id);
            
            if ($restored) {
                $restored->restore();
            }
        }
        elseif ($modelClass === 'App\Models\RequestCategory') {
            // ADD THIS BLOCK
            $restored = RequestCategory::withTrashed()
                ->find($this->model_id);
            
            if ($restored) {
                $restored->restore();
            }
        }
        else {
            // Recreate from stored data
            $model = new $modelClass();
            $model->id = $this->model_id;
            foreach ($this->data as $key => $value) {
                $model->$key = $value;
            }
            $model->save();
        }

        $this->update([
            'restored_at' => now(),
            'restored_by' => auth()->id(),
        ]);

        return true;
    } catch (\Exception $e) {
        \Log::error('Error restoring item from recycle bin', [
            'model_type' => $this->model_type,
            'model_id' => $this->model_id,
            'error' => $e->getMessage()
        ]);
        return false;
    }
}

    /**
     * UPDATED: Permanently delete item from recycle bin
     * Now handles UserRegistration force delete
     */
    public static function permanentlyDelete($item): bool
    {
        try {
            $modelClass = $item->model_type;
            
            if ($modelClass === 'App\Models\FishrApplication') {
                // Get the trashed record first
                $registration = \App\Models\FishrApplication::withTrashed()
                    ->find($item->model_id);
                
                if ($registration) {
                    // Delete all annexes first
                    $annexes = $registration->annexes;
                    foreach ($annexes as $annex) {
                        if ($annex->file_path && \Storage::disk('public')->exists($annex->file_path)) {
                            \Storage::disk('public')->delete($annex->file_path);
                        }
                        $annex->forceDelete();
                    }
                    
                    // Delete main document
                    if ($registration->document_path && \Storage::disk('public')->exists($registration->document_path)) {
                        \Storage::disk('public')->delete($registration->document_path);
                    }
                    
                    // Force delete the registration
                    $registration->forceDelete();
                }
            }
            elseif ($modelClass === 'App\Models\TrainingApplication') {
                // Delete document if exists
                if ($item->data['document_path'] ?? null) {
                    \Storage::disk('public')->delete($item->data['document_path']);
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
            // NEW: Handle UserRegistration - delete documents and force delete
            elseif ($modelClass === 'App\Models\UserRegistration') {
                // Delete uploaded documents if they exist
                if ($item->data['id_front_path'] ?? null) {
                    \Storage::disk('public')->delete($item->data['id_front_path']);
                }
                
                if ($item->data['id_back_path'] ?? null) {
                    \Storage::disk('public')->delete($item->data['id_back_path']);
                }
                
                if ($item->data['location_document_path'] ?? null) {
                    \Storage::disk('public')->delete($item->data['location_document_path']);
                }
                
                if ($item->data['profile_image_url'] ?? null) {
                    \Storage::disk('public')->delete($item->data['profile_image_url']);
                }
                
                // Force delete the registration record
                UserRegistration::withTrashed()
                    ->where('id', $item->model_id)
                    ->forceDelete();
            }
                elseif ($modelClass === 'App\Models\CategoryItem') {
            // ADD THIS BLOCK
            CategoryItem::withTrashed()
                ->where('id', $this->model_id)
                ->forceDelete();
        }
        elseif ($modelClass === 'App\Models\RequestCategory') {
            // ADD THIS BLOCK
            RequestCategory::withTrashed()
                ->where('id', $this->model_id)
                ->forceDelete();
        }

            $item->forceDelete();

            return true;
        } catch (\Exception $e) {
            \Log::error('Error permanently deleting item from recycle bin', [
                'model_type' => $item->model_type,
                'model_id' => $item->model_id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Bulk restore items
     */
    public function bulkRestore(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            $restored = 0;

            foreach ($ids as $id) {
                $item = RecycleBin::find($id);
                if ($item && RecycleBinService::restore($item)) {
                    $restored++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "{$restored} item(s) restored successfully",
                'count' => $restored
            ]);

        } catch (\Exception $e) {
            Log::error('Error bulk restoring items', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error restoring items'
            ], 500);
        }
    }

    /**
     * Bulk permanently delete items
     */
    public function bulkDestroy(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            $deleted = 0;

            foreach ($ids as $id) {
                $item = RecycleBin::find($id);
                if ($item && RecycleBinService::permanentlyDelete($item)) {
                    $deleted++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "{$deleted} item(s) permanently deleted",
                'count' => $deleted
            ]);

        } catch (\Exception $e) {
            Log::error('Error bulk deleting items', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting items'
            ], 500);
        }
    }

    /**
     * Empty recycle bin (delete expired items)
     */
    public function empty(Request $request)
    {
        try {
            $deleted = RecycleBinService::emptyExpired();

            return response()->json([
                'success' => true,
                'message' => "{$deleted} expired item(s) permanently deleted",
                'count' => $deleted
            ]);

        } catch (\Exception $e) {
            Log::error('Error emptying recycle bin', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error emptying recycle bin'
            ], 500);
        }
    }

    /**
     * Get recycle bin statistics
     */
    public function stats()
    {
        try {
            $stats = RecycleBinService::getStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting recycle bin statistics', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error getting statistics'
            ], 500);
        }
    }
}