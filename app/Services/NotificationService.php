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

    
    /**
     * SUPPLY MANAGEMENT NOTIFICATIONS
     * ================================
     */

    // ==========================================
    // CATEGORY NOTIFICATIONS
    // ==========================================

    public static function supplyManagementCategoryCreated($category)
    {
        $title = "New Supply Category Created";
        $message = "Category '{$category->display_name}' has been created and is ready for items";
        
        AdminNotification::notifyAdmins(
            'supply_category_created',
            $title,
            $message,
            [
                'category_id' => $category->id,
                'category_name' => $category->display_name,
                'icon' => $category->icon
            ],
            route('admin.seedlings.supply-management.index') . '?category=' . $category->id,
            'supply'
        );
    }

    public static function supplyManagementCategoryUpdated($category, array $changes)
    {
        $changedFields = implode(', ', array_keys($changes));
        $title = "Supply Category Updated";
        $message = "Category '{$category->display_name}' has been updated ({$changedFields})";
        
        AdminNotification::notifyAdmins(
            'supply_category_updated',
            $title,
            $message,
            [
                'category_id' => $category->id,
                'category_name' => $category->display_name,
                'changes' => $changes
            ],
            route('admin.seedlings.supply-management.index') . '?category=' . $category->id,
            'supply'
        );
    }

    public static function supplyManagementCategoryDeleted($categoryName, $itemCount)
    {
        $title = "Supply Category Deleted";
        $message = "Category '{$categoryName}' with {$itemCount} item(s) has been permanently deleted";
        
        AdminNotification::notifyAdmins(
            'supply_category_deleted',
            $title,
            $message,
            [
                'category_name' => $categoryName,
                'items_deleted' => $itemCount
            ],
            route('admin.seedlings.supply-management.index'),
            'supply'
        );
    }

    public static function supplyManagementCategoryActivated($category)
    {
        $title = "Supply Category Activated";
        $message = "Category '{$category->display_name}' is now active and available for requests";
        
        AdminNotification::notifyAdmins(
            'supply_category_activated',
            $title,
            $message,
            [
                'category_id' => $category->id,
                'category_name' => $category->display_name
            ],
            route('admin.seedlings.supply-management.index') . '?category=' . $category->id,
            'supply'
        );
    }

    public static function supplyManagementCategoryDeactivated($category)
    {
        $title = "Supply Category Deactivated";
        $message = "Category '{$category->display_name}' is no longer available for requests";
        
        AdminNotification::notifyAdmins(
            'supply_category_deactivated',
            $title,
            $message,
            [
                'category_id' => $category->id,
                'category_name' => $category->display_name
            ],
            route('admin.seedlings.supply-management.index') . '?category=' . $category->id,
            'supply'
        );
    }

    // ==========================================
    // ITEM MANAGEMENT NOTIFICATIONS
    // ==========================================

    public static function supplyManagementItemCreated($item)
    {
        $title = "New Supply Item Added";
        $message = "Item '{$item->name}' has been added to {$item->category->display_name}";
        
        AdminNotification::notifyAdmins(
            'supply_item_created',
            $title,
            $message,
            [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'category_id' => $item->category_id,
                'category_name' => $item->category->display_name,
                'initial_supply' => $item->current_supply,
                'unit' => $item->unit
            ],
            route('admin.seedlings.supply-management.index') . '?category=' . $item->category_id,
            'supply'
        );
    }

    public static function supplyManagementItemUpdated($item, array $changes)
    {
        $changedFields = implode(', ', array_keys($changes));
        $title = "Supply Item Updated";
        $message = "Item '{$item->name}' in {$item->category->display_name} has been updated ({$changedFields})";
        
        AdminNotification::notifyAdmins(
            'supply_item_updated',
            $title,
            $message,
            [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'category_id' => $item->category_id,
                'category_name' => $item->category->display_name,
                'changes' => $changes
            ],
            route('admin.seedlings.supply-management.index') . '?category=' . $item->category_id,
            'supply'
        );
    }

    public static function supplyManagementItemDeleted($itemName, $categoryName)
    {
        $title = "Supply Item Deleted";
        $message = "Item '{$itemName}' from {$categoryName} has been permanently deleted";
        
        AdminNotification::notifyAdmins(
            'supply_item_deleted',
            $title,
            $message,
            [
                'item_name' => $itemName,
                'category_name' => $categoryName
            ],
            route('admin.seedlings.supply-management.index'),
            'supply'
        );
    }

    public static function supplyManagementItemActivated($item)
    {
        $title = "Supply Item Activated";
        $message = "Item '{$item->name}' is now active and available";
        
        AdminNotification::notifyAdmins(
            'supply_item_activated',
            $title,
            $message,
            [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'category_id' => $item->category_id
            ],
            route('admin.seedlings.supply-management.index') . '?category=' . $item->category_id,
            'supply'
        );
    }

    public static function supplyManagementItemDeactivated($item)
    {
        $title = "Supply Item Deactivated";
        $message = "Item '{$item->name}' is no longer available";
        
        AdminNotification::notifyAdmins(
            'supply_item_deactivated',
            $title,
            $message,
            [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'category_id' => $item->category_id
            ],
            route('admin.seedlings.supply-management.index') . '?category=' . $item->category_id,
            'supply'
        );
    }

    // ==========================================
    // SUPPLY TRANSACTION NOTIFICATIONS
    // ==========================================

    public static function supplyManagementSupplyAdded($item, $quantity, $source = null)
    {
        $sourceText = $source ? " from {$source}" : '';
        $title = "Supply Received - {$item->name}";
        $message = "{$quantity} {$item->unit} of {$item->name} received{$sourceText}";
        
        AdminNotification::notifyAdmins(
            'supply_added',
            $title,
            $message,
            [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'category_id' => $item->category_id,
                'quantity_added' => $quantity,
                'new_supply' => $item->current_supply,
                'unit' => $item->unit,
                'source' => $source
            ],
            route('admin.seedlings.supply-management.index') . '?category=' . $item->category_id,
            'supply'
        );
    }

    public static function supplyManagementSupplyAdjusted($item, $oldSupply, $newSupply, $reason)
    {
        $change = $newSupply - $oldSupply;
        $direction = $change > 0 ? 'increased' : 'decreased';
        $absoluteChange = abs($change);
        
        $title = "Supply Adjusted - {$item->name}";
        $message = "{$item->name} supply {$direction} by {$absoluteChange} {$item->unit} ({$reason})";
        
        AdminNotification::notifyAdmins(
            'supply_adjusted',
            $title,
            $message,
            [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'category_id' => $item->category_id,
                'old_supply' => $oldSupply,
                'new_supply' => $newSupply,
                'adjustment' => $change,
                'unit' => $item->unit,
                'reason' => $reason
            ],
            route('admin.seedlings.supply-management.index') . '?category=' . $item->category_id,
            'supply'
        );
    }

    public static function supplyManagementSupplyLossRecorded($item, $quantity, $reason)
    {
        $title = "Supply Loss Recorded - {$item->name}";
        $message = "{$quantity} {$item->unit} of {$item->name} lost ({$reason})";
        
        AdminNotification::notifyAdmins(
            'supply_loss_recorded',
            $title,
            $message,
            [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'category_id' => $item->category_id,
                'quantity_lost' => $quantity,
                'remaining_supply' => $item->current_supply,
                'unit' => $item->unit,
                'loss_reason' => $reason
            ],
            route('admin.seedlings.supply-management.index') . '?category=' . $item->category_id,
            'supply'
        );
    }

    // ==========================================
    // CRITICAL SUPPLY ALERTS
    // ==========================================

    public static function supplyManagementCriticalLowStock($item)
    {
        if ($item->current_supply > $item->reorder_point) {
            return; // Only notify if truly low
        }
        
        $title = "CRITICAL: Low Stock Alert - {$item->name}";
        $message = "{$item->name} is critically low! Current: {$item->current_supply} {$item->unit}, Reorder Point: {$item->reorder_point}";
        
        AdminNotification::notifyAdmins(
            'supply_critical_low',
            $title,
            $message,
            [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'category_id' => $item->category_id,
                'current_supply' => $item->current_supply,
                'reorder_point' => $item->reorder_point,
                'unit' => $item->unit,
                'severity' => 'critical'
            ],
            route('admin.seedlings.supply-management.index') . '?category=' . $item->category_id,
            'supply'
        );
    }

    public static function supplyManagementOutOfStock($item)
    {
        if ($item->current_supply > 0) {
            return; // Only notify if completely out
        }
        
        $title = "OUT OF STOCK - {$item->name}";
        $message = "{$item->name} is completely out of stock! Immediate restock required.";
        
        AdminNotification::notifyAdmins(
            'supply_out_of_stock',
            $title,
            $message,
            [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'category_id' => $item->category_id,
                'unit' => $item->unit,
                'severity' => 'critical'
            ],
            route('admin.seedlings.supply-management.index') . '?category=' . $item->category_id,
            'supply'
        );
    }

    public static function supplyManagementMaximumCapacityReached($item)
    {
        if ($item->current_supply < $item->maximum_supply) {
            return; // Only notify if at max
        }
        
        $title = "Maximum Capacity Reached - {$item->name}";
        $message = "{$item->name} has reached maximum storage capacity ({$item->maximum_supply} {$item->unit})";
        
        AdminNotification::notifyAdmins(
            'supply_max_capacity',
            $title,
            $message,
            [
                'item_id' => $item->id,
                'item_name' => $item->name,
                'category_id' => $item->category_id,
                'current_supply' => $item->current_supply,
                'maximum_supply' => $item->maximum_supply,
                'unit' => $item->unit
            ],
            route('admin.seedlings.supply-management.index') . '?category=' . $item->category_id,
            'supply'
        );
    }
}