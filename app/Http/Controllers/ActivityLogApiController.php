<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;

class ActivityLogApiController extends Controller
{
    /**
     * Get activity details for modal
     * GET /api/activity-logs/{id}
     */
    public function show($id)
    {
        if (!Auth::user() || !Auth::user()->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            $activity = Activity::with(['causer', 'subject'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $activity->id,
                    'event' => $activity->event,
                    'created_at' => $activity->created_at->format('M d, Y H:i:s'),
                    'causer_name' => $activity->causer?->name ?? 'System',
                    'causer_email' => $activity->causer?->email,
                    'subject_type_label' => $this->formatModelLabel($activity->subject_type),
                    'subject_type' => $activity->subject_type,
                    'subject_id' => $activity->subject_id,
                    'description' => $activity->description,
                    'properties' => $activity->properties
                ]
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Activity not found'
            ], 404);
        }
    }

    /**
     * Format model label
     */
    private function formatModelLabel($modelType): string
    {
        if (!$modelType) return 'System';

        $mapping = [
            'App\Models\User' => 'User Account',
            'App\Models\UserRegistration' => 'User Registration',
            'App\Models\RsbsaApplication' => 'RSBSA Application',
            'App\Models\FishrApplication' => 'FishR Application',
            'App\Models\BoatrApplication' => 'BoatR Application',
            'App\Models\TrainingApplication' => 'Training Application',
            'App\Models\SeedlingRequest' => 'Seedling Request',
            'App\Models\CategoryItem' => 'Supply Item',
            'App\Models\ItemSupplyLog' => 'Supply Log',
            'App\Models\BoatrAnnex' => 'BoatR Document',
            'App\Models\FishrAnnex' => 'FishR Document',
        ];

        return $mapping[$modelType] ?? class_basename($modelType);
    }
}