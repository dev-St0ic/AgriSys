<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Facades\Activity;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    /**
     * Log a system activity
     * Used for tracking all critical business actions across the system
     * 
     * @param string $action - Action performed (e.g., 'created', 'updated', 'deleted', 'approved', 'rejected', 'login', 'logout', 'downloaded', 'exported')
     * @param string $model - Model type (e.g., 'BoatrApplication', 'User', 'Training')
     * @param int|null $modelId - ID of the affected model
     * @param array|null $properties - Additional context data (changes, reason, etc.)
     * @param string|null $description - Custom description for the log
     * 
     * @return void
     */
    protected function logActivity(
        string $action,
        string $model,
        ?int $modelId = null,
        ?array $properties = null,
        ?string $description = null
    ): void {
        try {
            $user = Auth::user();
            if (!$user) {
                return; // Don't log if no authenticated user
            }

            $desc = $description ?? "{$action} - {$model}" . ($modelId ? " (ID: {$modelId})" : '');

            // Add request context to properties
            $contextProperties = $properties ?? [];
            $contextProperties['ip_address'] = request()->ip();
            $contextProperties['user_agent'] = request()->userAgent();

            Activity::causedBy($user)
                ->withProperties($contextProperties)
                ->log($desc);
                
        } catch (\Exception $e) {
            \Log::error('Activity logging failed: ' . $e->getMessage(), [
                'action' => $action,
                'model' => $model,
                'modelId' => $modelId,
                'exception' => $e,
            ]);
        }
    }
}
