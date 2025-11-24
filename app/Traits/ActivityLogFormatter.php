<?php

namespace App\Traits;

/**
 * ActivityLogFormatter Trait
 * Provides helper methods for formatting activity logs in views
 * Usage: use ActivityLogFormatter; in your controller or model
 */
trait ActivityLogFormatter
{
    /**
     * Get user-friendly model name from fully qualified class name
     */
    public static function getModelLabel($modelType): string
    {
        if (!$modelType) {
            return 'System';
        }

        $modelName = class_basename($modelType);
        
        $mapping = [
            'User' => 'User Account',
            'UserRegistration' => 'User Registration',
            'RsbsaApplication' => 'RSBSA Application',
            'FishrApplication' => 'FishR Application',
            'BoatrApplication' => 'BoatR Application',
            'TrainingApplication' => 'Training Application',
            'SeedlingRequest' => 'Seedling Request',
            'SeedlingRequestItem' => 'Request Item',
            'CategoryItem' => 'Supply Item',
            'ItemSupplyLog' => 'Supply Transaction',
            'RequestCategory' => 'Item Category',
            'BoatrAnnex' => 'BoatR Document',
            'FishrAnnex' => 'FishR Document',
        ];

        return $mapping[$modelName] ?? $modelName;
    }

    /**
     * Get user-friendly field name
     */
    public static function getFieldLabel($fieldName): string
    {
        $mapping = [
            'first_name' => 'First Name',
            'middle_name' => 'Middle Name',
            'last_name' => 'Last Name',
            'name_extension' => 'Name Extension',
            'contact_number' => 'Contact Number',
            'email' => 'Email Address',
            'status' => 'Status',
            'remarks' => 'Remarks',
            'application_number' => 'Application #',
            'registration_number' => 'Registration #',
            'request_number' => 'Request #',
            'vessel_name' => 'Vessel Name',
            'boat_type' => 'Boat Type',
            'boat_length' => 'Boat Length',
            'boat_width' => 'Boat Width',
            'boat_depth' => 'Boat Depth',
            'engine_type' => 'Engine Type',
            'engine_horsepower' => 'Engine Horsepower',
            'primary_fishing_gear' => 'Fishing Gear',
            'current_supply' => 'Current Supply',
            'minimum_supply' => 'Minimum Supply',
            'maximum_supply' => 'Maximum Supply',
            'reorder_point' => 'Reorder Point',
            'supply_alert_enabled' => 'Alert Enabled',
            'approved_quantity' => 'Approved Quantity',
            'requested_quantity' => 'Requested Quantity',
            'total_quantity' => 'Total Quantity',
            'is_active' => 'Active',
            'display_order' => 'Display Order',
            'role' => 'Role',
            'name' => 'Name',
            'password' => 'Password',
            'document_path' => 'Document',
            'supporting_document_path' => 'Supporting Document',
            'user_document_path' => 'User Document',
            'inspection_documents' => 'Inspection Documents',
            'inspection_completed' => 'Inspection Completed',
            'inspection_date' => 'Inspection Date',
            'inspection_notes' => 'Inspection Notes',
            'documents_verified' => 'Documents Verified',
            'documents_verified_at' => 'Verified Date',
            'profile_photo' => 'Profile Photo',
            'date_of_birth' => 'Date of Birth',
            'barangay' => 'Barangay',
            'address' => 'Address',
            'main_livelihood' => 'Main Livelihood',
            'land_area' => 'Land Area',
            'farm_location' => 'Farm Location',
            'commodity' => 'Commodity',
            'training_type' => 'Training Type',
            'planting_location' => 'Planting Location',
            'preferred_delivery_date' => 'Delivery Date',
            'sex' => 'Gender',
            'age' => 'Age',
            'category_id' => 'Category',
            'category_item_id' => 'Item',
            'unit' => 'Unit',
            'min_quantity' => 'Min Quantity',
            'max_quantity' => 'Max Quantity',
            'reviewed_at' => 'Reviewed Date',
            'reviewed_by' => 'Reviewed By',
            'approved_at' => 'Approved Date',
            'approved_by' => 'Approved By',
            'rejected_at' => 'Rejected Date',
            'last_supplied_at' => 'Last Supplied',
            'last_supplied_by' => 'Supplied By',
            'fishr_number' => 'FishR Number',
            'username' => 'Username',
            'transaction_type' => 'Transaction Type',
            'quantity' => 'Quantity',
            'old_supply' => 'Previous Supply',
            'new_supply' => 'New Supply',
            'performed_by' => 'Performed By',
            'notes' => 'Notes',
            'source' => 'Source',
            'reference_type' => 'Reference',
            'reference_id' => 'Reference ID',
        ];

        return $mapping[$fieldName] ?? ucwords(str_replace('_', ' ', $fieldName));
    }

    /**
     * Check if field name is a date field
     */
    private static function isDateField($fieldName): bool
    {
        if (!$fieldName) {
            return false;
        }

        return str_contains($fieldName, 'date') || 
               str_ends_with($fieldName, '_at') ||
               str_ends_with($fieldName, '_on');
    }

    /**
     * Check if field name contains a keyword
     */
    private static function fieldContainsKeyword($fieldName, $keywords): bool
    {
        if (!$fieldName || !is_array($keywords)) {
            return false;
        }

        foreach ($keywords as $keyword) {
            if (str_contains($fieldName, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Format field value for display
     */
    public static function formatValue($value, $fieldName = null): string
    {
        // Handle null/empty values
        if (is_null($value) || $value === '') {
            return '<span class="text-muted">-</span>';
        }

        // Handle booleans
        if (is_bool($value)) {
            return $value 
                ? '<span class="badge bg-success">Yes</span>' 
                : '<span class="badge bg-secondary">No</span>';
        }

        // Handle dates
        if ($fieldName && self::isDateField($fieldName)) {
            if (is_string($value)) {
                try {
                    $date = \Carbon\Carbon::parse($value);
                    return $date->format('M d, Y h:i A');
                } catch (\Exception $e) {
                    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
                }
            }
        }

        // Handle status fields
        if ($fieldName && (str_ends_with($fieldName, '_status') || $fieldName === 'status')) {
            $statusColors = [
                'pending' => 'warning',
                'under_review' => 'info',
                'approved' => 'success',
                'rejected' => 'danger',
                'active' => 'success',
                'inactive' => 'secondary',
                'completed' => 'success',
                'cancelled' => 'secondary',
                'on_hold' => 'warning',
                'unverified' => 'secondary',
                'in_stock' => 'success',
                'low_stock' => 'warning',
                'out_of_stock' => 'danger',
                'in_supply' => 'success',
                'low_supply' => 'warning',
                'out_of_supply' => 'danger',
            ];

            $color = $statusColors[$value] ?? 'secondary';
            $label = ucwords(str_replace('_', ' ', $value));

            return "<span class=\"badge bg-{$color}\">{$label}</span>";
        }

        // Handle arrays/json
        if (is_array($value)) {
            if (empty($value)) {
                return '<span class="text-muted">No data</span>';
            }
            return htmlspecialchars(json_encode($value), ENT_QUOTES, 'UTF-8');
        }

        // Handle numeric values - quantity fields
        if (is_numeric($value) && $fieldName && self::fieldContainsKeyword($fieldName, ['quantity', 'supply', 'amount', 'count'])) {
            return "<strong>{$value}</strong>";
        }

        // Default: cast to string
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Get change indicator badge
     */
    public static function getChangeIndicator($oldValue, $newValue): string
    {
        if ($oldValue === $newValue) {
            return '<span class="badge bg-light text-dark">No Change</span>';
        }

        $isEmpty = function($val) {
            return $val === null || $val === '' || $val === false;
        };

        if ($isEmpty($oldValue) && !$isEmpty($newValue)) {
            return '<span class="badge bg-success">New</span>';
        }

        if (!$isEmpty($oldValue) && $isEmpty($newValue)) {
            return '<span class="badge bg-danger">Removed</span>';
        }

        return '<span class="badge bg-info">Modified</span>';
    }

    /**
     * Get event color
     */
    public static function getEventColor($event): string
    {
        $eventColors = [
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'danger',
            'restored' => 'warning',
            'login' => 'primary',
            'failed_login' => 'danger',
            'export' => 'secondary',
            'import' => 'secondary',
            'archived' => 'dark',
            'approved' => 'success',
            'rejected' => 'danger',
        ];

        return $eventColors[$event] ?? 'secondary';
    }

    /**
     * Get event icon
     */
    public static function getEventIcon($event): string
    {
        $eventIcons = [
            'created' => 'fa-plus-circle',
            'updated' => 'fa-edit',
            'deleted' => 'fa-trash',
            'restored' => 'fa-undo',
            'login' => 'fa-sign-in-alt',
            'failed_login' => 'fa-exclamation-triangle',
            'export' => 'fa-download',
            'import' => 'fa-upload',
            'archived' => 'fa-archive',
            'approved' => 'fa-check-circle',
            'rejected' => 'fa-times-circle',
        ];

        return $eventIcons[$event] ?? 'fa-circle';
    }

    /**
     * Get transaction type label
     */
    public static function getTransactionTypeLabel($type): string
    {
        $labels = [
            'received' => 'Received',
            'distributed' => 'Distributed',
            'returned' => 'Returned',
            'adjustment' => 'Adjusted',
            'loss' => 'Loss/Damaged',
            'initial_supply' => 'Initial Supply',
        ];

        return $labels[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    /**
     * Get transaction type color
     */
    public static function getTransactionTypeColor($type): string
    {
        $colors = [
            'received' => 'success',
            'distributed' => 'primary',
            'returned' => 'info',
            'adjustment' => 'warning',
            'loss' => 'danger',
            'initial_supply' => 'secondary',
        ];

        return $colors[$type] ?? 'secondary';
    }

    /**
     * Format changes array for display
     */
    public static function formatChanges($properties): array
    {
        if (!$properties || !isset($properties['attributes'])) {
            return [];
        }

        $changes = [];
        $attributes = $properties['attributes'] ?? [];
        $oldValues = $properties['old'] ?? [];

        foreach ($attributes as $key => $newValue) {
            $oldValue = $oldValues[$key] ?? null;

            $changes[] = [
                'field' => self::getFieldLabel($key),
                'old_value' => self::formatValue($oldValue, $key),
                'new_value' => self::formatValue($newValue, $key),
                'indicator' => self::getChangeIndicator($oldValue, $newValue),
            ];
        }

        return $changes;
    }

    /**
     * Get activity context message
     */
    public static function getActivityContext($activity): string
    {
        $user = $activity->causer ? $activity->causer->name : 'System';
        $model = self::getModelLabel($activity->subject_type);
        $action = $activity->description ?? strtolower($activity->event ?? 'performed action');

        if ($activity->subject_id) {
            return "{$user} {$action} {$model} (ID: {$activity->subject_id})";
        }

        return "{$user} {$action}";
    }
}