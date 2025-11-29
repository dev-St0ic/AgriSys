<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\SeedlingRequest;
use App\Models\CategoryItem;

class NotificationService
{
    public static function seedlingRequestCreated(SeedlingRequest $request)
    {
        $title = "New Seedling Request";
        $message = "{$request->full_name} has submitted a new seedling request ({$request->request_number})";
        
        AdminNotification::notifyAdmins(
            'seedling_request_new',
            $title,
            $message,
            [
                'request_id' => $request->id,
                'request_number' => $request->request_number,
                'barangay' => $request->barangay,
                'total_items' => $request->items->count()
            ],
            route('admin.seedlings.requests') . '?search=' . $request->request_number
        );
    }

    public static function seedlingRequestStatusChanged(SeedlingRequest $request, string $oldStatus)
    {
        $statusMessages = [
            'approved' => "approved",
            'rejected' => "rejected",
            'partially_approved' => "partially approved",
            'under_review' => "moved to under review"
        ];

        $action = $statusMessages[$request->status] ?? "updated";
        
        $title = "Seedling Request " . ucfirst($action);
        $message = "Request {$request->request_number} from {$request->full_name} has been {$action}";
        
        $type = match($request->status) {
            'approved' => 'seedling_request_approved',
            'rejected' => 'seedling_request_rejected',
            default => 'seedling_request_updated'
        };

        AdminNotification::notifyAdmins(
            $type,
            $title,
            $message,
            [
                'request_id' => $request->id,
                'request_number' => $request->request_number,
                'old_status' => $oldStatus,
                'new_status' => $request->status
            ],
            route('admin.seedlings.requests') . '?search=' . $request->request_number
        );
    }

    public static function seedlingStockLow(CategoryItem $item)
    {
        if ($item->current_supply <= $item->minimum_stock_level && $item->current_supply > 0) {
            $title = "Low Stock Alert";
            $message = "{$item->name} is running low (Current: {$item->current_supply} {$item->unit}, Minimum: {$item->minimum_stock_level})";
            
            AdminNotification::notifyAdmins(
                'seedling_stock_low',
                $title,
                $message,
                [
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'current_supply' => $item->current_supply,
                    'minimum_level' => $item->minimum_stock_level,
                    'category_id' => $item->category_id
                ],
                route('admin.seedlings.supply-management.index')
            );
        }
    }

    public static function seedlingStockOut(CategoryItem $item)
    {
        if ($item->current_supply <= 0) {
            $title = "Out of Stock Alert";
            $message = "{$item->name} is out of stock! Restock immediately.";
            
            AdminNotification::notifyAdmins(
                'seedling_stock_out',
                $title,
                $message,
                [
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'category_id' => $item->category_id
                ],
                route('admin.seedlings.supply-management.index')
            );
        }
    }

    public static function seedlingRequestDeleted(SeedlingRequest $request)
    {
        $title = "Seedling Request Deleted";
        $message = "Request {$request->request_number} from {$request->full_name} has been deleted";
        
        AdminNotification::notifyAdmins(
            'seedling_request_updated',
            $title,
            $message,
            [
                'request_number' => $request->request_number,
                'deleted_by' => auth()->user()->name ?? 'System'
            ],
            route('admin.seedlings.requests')
        );
    }
}