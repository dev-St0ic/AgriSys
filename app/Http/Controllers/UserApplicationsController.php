<?php

namespace App\Http\Controllers;

use App\Models\RsbsaApplication;
use App\Models\SeedlingRequest;
use App\Models\FishrApplication;
use App\Models\BoatrApplication;
use App\Models\TrainingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserApplicationsController extends Controller
{
    /**
     * Get all applications for the authenticated user
     * Combines RSBSA, Seedlings, FishR, BoatR, and Training applications
     */
    public function getAllApplications(Request $request)
    {
        try {
            // Get user ID from session
            $userId = session('user.id');
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in to view your applications'
                ], 401);
            }

            Log::info('Fetching all applications for user', ['user_id' => $userId]);

            $allApplications = [];

            // 1. Fetch RSBSA Applications
            $rsbsaApps = RsbsaApplication::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get();

            foreach ($rsbsaApps as $app) {
                $allApplications[] = [
                    'id' => $app->id,
                    'type' => 'RSBSA Registration',
                    'application_number' => $app->application_number,
                    'reference_number' => $app->application_number,
                    'status' => $app->status,
                    'description' => 'Registry System for Basic Sectors in Agriculture',
                    'full_name' => $app->full_name,
                    'livelihood' => $app->main_livelihood,
                    'barangay' => $app->barangay,
                    'remarks' => $app->remarks,
                    'submitted_at' => $app->created_at->format('Y-m-d H:i:s'),
                    'date' => $app->created_at->format('Y-m-d'),
                    'created_at' => $app->created_at->format('M d, Y'),
                    'sort_date' => $app->created_at
                ];
            }

            // 2. Fetch Seedling Requests (if table exists)
            try {
                if (class_exists('App\Models\SeedlingRequest')) {
                    $seedlingApps = SeedlingRequest::where('user_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->get();

                    foreach ($seedlingApps as $app) {
                        $allApplications[] = [
                            'id' => $app->id,
                            'type' => 'Seedlings Request',
                            'application_number' => $app->application_number ?? $app->reference_number ?? 'SL-' . $app->id,
                            'reference_number' => $app->reference_number ?? 'SL-' . $app->id,
                            'status' => $app->status,
                            'description' => 'Request for agricultural seedlings',
                            'full_name' => $app->full_name ?? $app->name ?? 'N/A',
                            'barangay' => $app->barangay ?? null,
                            'remarks' => $app->remarks ?? null,
                            'submitted_at' => $app->created_at->format('Y-m-d H:i:s'),
                            'date' => $app->created_at->format('Y-m-d'),
                            'created_at' => $app->created_at->format('M d, Y'),
                            'sort_date' => $app->created_at
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Could not fetch seedling applications: ' . $e->getMessage());
            }

            // 3. Fetch FishR Applications (if table exists)
            try {
                if (class_exists('App\Models\FishrApplication')) {
                    $fishrApps = FishrApplication::where('user_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->get();

                    foreach ($fishrApps as $app) {
                        $allApplications[] = [
                            'id' => $app->id,
                            'type' => 'FishR Registration',
                            'application_number' => $app->application_number ?? 'FR-' . $app->id,
                            'reference_number' => $app->application_number ?? 'FR-' . $app->id,
                            'status' => $app->status,
                            'description' => 'Fisherfolk registration',
                            'full_name' => $app->full_name ?? $app->name ?? 'N/A',
                            'barangay' => $app->barangay ?? null,
                            'remarks' => $app->remarks ?? null,
                            'submitted_at' => $app->created_at->format('Y-m-d H:i:s'),
                            'date' => $app->created_at->format('Y-m-d'),
                            'created_at' => $app->created_at->format('M d, Y'),
                            'sort_date' => $app->created_at
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Could not fetch FishR applications: ' . $e->getMessage());
            }

            // 4. Fetch BoatR Applications (if table exists)
            try {
                if (class_exists('App\Models\BoatrApplication')) {
                    $boatrApps = BoatrApplication::where('user_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->get();

                    foreach ($boatrApps as $app) {
                        $allApplications[] = [
                            'id' => $app->id,
                            'type' => 'BoatR Registration',
                            'application_number' => $app->application_number ?? 'BR-' . $app->id,
                            'reference_number' => $app->application_number ?? 'BR-' . $app->id,
                            'status' => $app->status,
                            'description' => 'Fishing boat registration',
                            'full_name' => $app->full_name ?? $app->name ?? 'N/A',
                            'barangay' => $app->barangay ?? null,
                            'remarks' => $app->remarks ?? null,
                            'submitted_at' => $app->created_at->format('Y-m-d H:i:s'),
                            'date' => $app->created_at->format('Y-m-d'),
                            'created_at' => $app->created_at->format('M d, Y'),
                            'sort_date' => $app->created_at
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Could not fetch BoatR applications: ' . $e->getMessage());
            }

            // 5. Fetch Training Requests (if table exists)
            try {
                if (class_exists('App\Models\TrainingRequest')) {
                    $trainingApps = TrainingRequest::where('user_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->get();

                    foreach ($trainingApps as $app) {
                        $allApplications[] = [
                            'id' => $app->id,
                            'type' => 'Training Request',
                            'application_number' => $app->application_number ?? 'TR-' . $app->id,
                            'reference_number' => $app->application_number ?? 'TR-' . $app->id,
                            'status' => $app->status,
                            'description' => 'Agricultural training program request',
                            'full_name' => $app->full_name ?? $app->name ?? 'N/A',
                            'barangay' => $app->barangay ?? null,
                            'remarks' => $app->remarks ?? null,
                            'submitted_at' => $app->created_at->format('Y-m-d H:i:s'),
                            'date' => $app->created_at->format('Y-m-d'),
                            'created_at' => $app->created_at->format('M d, Y'),
                            'sort_date' => $app->created_at
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Could not fetch training applications: ' . $e->getMessage());
            }

            // Sort all applications by date (newest first)
            usort($allApplications, function($a, $b) {
                return $b['sort_date'] <=> $a['sort_date'];
            });

            // Remove sort_date field from response
            $allApplications = array_map(function($app) {
                unset($app['sort_date']);
                return $app;
            }, $allApplications);

            Log::info('Successfully fetched applications', [
                'user_id' => $userId,
                'total_count' => count($allApplications),
                'rsbsa_count' => $rsbsaApps->count()
            ]);

            return response()->json([
                'success' => true,
                'applications' => $allApplications,
                'total' => count($allApplications),
                'breakdown' => [
                    'rsbsa' => $rsbsaApps->count(),
                    // Add other counts as needed
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching user applications', [
                'user_id' => $userId ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load applications',
                'error' => config('app.debug') ? $e->getMessage() : 'Server error'
            ], 500);
        }
    }

    /**
     * Get RSBSA applications only
     */
    public function getRsbsaApplications(Request $request)
    {
        try {
            $userId = session('user.id');
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in to view your applications'
                ], 401);
            }

            $applications = RsbsaApplication::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($app) {
                    return [
                        'id' => $app->id,
                        'type' => 'RSBSA Registration',
                        'application_number' => $app->application_number,
                        'status' => $app->status,
                        'description' => 'Registry System for Basic Sectors in Agriculture',
                        'full_name' => $app->full_name,
                        'livelihood' => $app->main_livelihood,
                        'barangay' => $app->barangay,
                        'remarks' => $app->remarks,
                        'submitted_at' => $app->created_at->format('M d, Y h:i A'),
                        'date' => $app->created_at->format('Y-m-d'),
                    ];
                });

            return response()->json([
                'success' => true,
                'applications' => $applications
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching RSBSA applications: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load RSBSA applications'
            ], 500);
        }
    }
}