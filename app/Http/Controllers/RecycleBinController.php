<?php

namespace App\Http\Controllers;

use App\Models\RecycleBin;
use App\Models\UserRegistration;
use App\Models\FishrApplication;
use App\Models\FishrAnnex;
use App\Models\SeedlingRequest;
use App\Models\CategoryItem;
use App\Models\RequestCategory;
use App\Models\BoatrApplication;
use App\Models\BoatrAnnex;
use App\Models\RsbsaApplication;
use App\Models\TrainingApplication;
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
                    'fishr_annex' => 'App\Models\FishrAnnex', 
                    'boatr_annex' => 'App\Models\BoatrAnnex',  
                    'rsbsa' => 'App\Models\RsbsaApplication',
                    'seedlings' => 'App\Models\SeedlingRequest',
                    'training' => 'App\Models\TrainingApplication',
                    'user_registration' => 'App\Models\UserRegistration',
                    'category_item' => 'App\Models\CategoryItem',
                    'request_category' => 'App\Models\RequestCategory',
                ];

                if (isset($typeMap[$request->type])) {
                    $query->where('model_type', $typeMap[$request->type]);
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
                          ->paginate(10)
                          ->appends($request->query());

                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'data' => $items,
                        'view' => view('admin.recycle-bin.partials.table', compact('items'))->render()
                    ]);
                }

            return view('admin.recycle-bin.index', compact('items'));

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
                    'deleted_at' => $item->deleted_at->format('M d, Y h:i A'),
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
     * Restore item from recycle bin
     */
    public function restore($id)
    {
        try {
            $item = RecycleBin::findOrFail($id);

            // Use the service for restoration
            if (RecycleBinService::restore($item)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item restored successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to restore item'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Error restoring item from recycle bin', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error restoring item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permanently delete item from recycle bin (hard delete)
     */
    public function permanentlyDelete($id)
    {
        try {
            $item = RecycleBin::findOrFail($id);
            $itemName = $item->item_name;

            // Delete from recycle bin
            $item->delete();

            // TODO: Optionally also permanently delete the soft-deleted model from the original table
            // This depends on your business logic - currently we just remove from recycle bin

            return response()->json([
                'success' => true,
                'message' => "'{$itemName}' has been permanently deleted"
            ]);

        } catch (\Exception $e) {
            Log::error('Error permanently deleting item from recycle bin', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete item from recycle bin (remove record only)
     */
    public function destroy($id)
    {
        try {
            $item = RecycleBin::findOrFail($id);

            // Just delete from recycle bin (don't permanently delete the model)
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from recycle bin'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting item from recycle bin', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting item: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk restore items
     */
    public function bulkRestore(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items selected'
                ], 422);
            }

            $restored = 0;
            $failed = [];

            foreach ($ids as $id) {
                $item = RecycleBin::find($id);
                if ($item) {
                    if (RecycleBinService::restore($item)) {
                        $restored++;
                    } else {
                        $failed[] = $item->item_name;
                    }
                } else {
                    $failed[] = "Item #{$id} not found";
                }
            }

            $message = "{$restored} item(s) restored successfully";
            if (!empty($failed)) {
                $message .= ". Failed: " . implode(", ", $failed);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $restored,
                'failed_count' => count($failed)
            ]);

        } catch (\Exception $e) {
            Log::error('Error bulk restoring items', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error restoring items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk permanently delete items
     */
    public function bulkPermanentlyDelete(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items selected'
                ], 422);
            }

            $deleted = 0;
            $failed = [];

            foreach ($ids as $id) {
                $item = RecycleBin::find($id);
                if ($item) {
                    try {
                        // Delete from recycle bin
                        $item->delete();
                        $deleted++;
                    } catch (\Exception $e) {
                        $failed[] = $item->item_name . ' (' . $e->getMessage() . ')';
                    }
                } else {
                    $failed[] = "Item #{$id} not found";
                }
            }

            $message = "{$deleted} item(s) permanently deleted";
            if (!empty($failed)) {
                $message .= ". Failed: " . implode(", ", $failed);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $deleted,
                'failed_count' => count($failed)
            ]);

        } catch (\Exception $e) {
            Log::error('Error bulk permanently deleting items', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete items from recycle bin
     */
    public function bulkDestroy(Request $request)
    {
        try {
            $ids = $request->input('ids', []);
            
            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items selected'
                ], 422);
            }

            $deleted = 0;
            $failed = [];

            foreach ($ids as $id) {
                $item = RecycleBin::find($id);
                if ($item) {
                    try {
                        $item->delete();
                        $deleted++;
                    } catch (\Exception $e) {
                        $failed[] = $item->item_name;
                    }
                } else {
                    $failed[] = "Item #{$id} not found";
                }
            }

            $message = "{$deleted} item(s) removed from recycle bin";
            if (!empty($failed)) {
                $message .= ". Failed: " . implode(", ", $failed);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $deleted,
                'failed_count' => count($failed)
            ]);

        } catch (\Exception $e) {
            Log::error('Error bulk deleting items', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error deleting items: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Empty entire recycle bin (all non-restored items)
     */
    public function empty(Request $request)
    {
        try {
            $items = RecycleBin::notRestored()->get();
            
            if ($items->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Recycle bin is already empty'
                ], 422);
            }

            $count = $items->count();
            $deleted = 0;

            foreach ($items as $item) {
                try {
                    $item->delete();
                    $deleted++;
                } catch (\Exception $e) {
                    Log::warning("Could not delete recycle bin item {$item->id}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Recycle bin emptied. {$deleted} of {$count} items deleted",
                'count' => $deleted
            ]);

        } catch (\Exception $e) {
            Log::error('Error emptying recycle bin', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error emptying recycle bin: ' . $e->getMessage()
            ], 500);
        }
    }
}