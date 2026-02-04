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
     * FIXED: Restore item from recycle bin
     * Now properly delegates to the RecycleBinService
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
     * FIXED: Permanently delete item from recycle bin
     * Now properly delegates to the RecycleBinService
     */
    public function destroy($id)
    {
        try {
            $item = RecycleBin::findOrFail($id);

            // Use the service for permanent deletion
            if (RecycleBinService::permanentlyDelete($item)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item permanently deleted'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete item'
                ], 500);
            }

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

  public function bulkRestore(Request $request)
{
    try {
        $ids = $request->input('ids', []);
        $restored = 0;
        $failed = [];

        foreach ($ids as $id) {
            $item = RecycleBin::find($id);
            if ($item) {
                if (RecycleBinService::restore($item)) {
                    $restored++;
                } else {
                    $failed[] = $item->item_name; // ✅ Track failures
                }
            } else {
                $failed[] = "Item #{$id} not found"; // ✅ Track missing items
            }
        }

        $message = "{$restored} item(s) restored successfully";
        if (!empty($failed)) {
            $message .= ". Failed: " . implode(", ", $failed); // ✅ Report failures
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'count' => $restored,
            'failed_count' => count($failed)  // ✅ Return failed count
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

// ✅ Same fix for bulkDestroy()
public function bulkDestroy(Request $request)
{
    try {
        $ids = $request->input('ids', []);
        $deleted = 0;
        $failed = [];

        foreach ($ids as $id) {
            $item = RecycleBin::find($id);
            if ($item) {
                if (RecycleBinService::permanentlyDelete($item)) {
                    $deleted++;
                } else {
                    $failed[] = $item->item_name;
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