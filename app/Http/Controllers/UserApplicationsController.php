<?php

namespace App\Http\Controllers;

use App\Models\RsbsaApplication;
use App\Models\SeedlingRequest;
use App\Models\FishrApplication;
use App\Models\BoatrApplication;
use App\Models\TrainingApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserApplicationsController extends Controller
{
    public function getAllApplications(Request $request)
    {
        try {
            $userId = session('user.id');

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in to view your applications'
                ], 401);
            }

            Log::info('Fetching all applications for user', ['user_id' => $userId]);

            $allApplications = [];

            // FETCH RSBSA APPLICATIONS
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
                    'sort_date' => $app->created_at,
                    'updated_at' => $app->updated_at
                ];
            }

            // FETCH SEEDLING REQUESTS (Display as "Supply Request")
            try {
                if (class_exists('App\Models\SeedlingRequest')) {
                    $seedlingApps = SeedlingRequest::where('user_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->get();

                    foreach ($seedlingApps as $app) {
                        $allApplications[] = [
                            'id' => $app->id,
                            'type' => 'Supply Request',
                            'application_number' => $app->request_number ?? 'SL-' . $app->id,
                            'reference_number' => $app->request_number ?? 'SL-' . $app->id,
                            'status' => $app->status,
                            'description' => 'Request for agricultural supplies',
                            'full_name' => $app->full_name ?? $app->name ?? 'N/A',
                            'barangay' => $app->barangay ?? null,
                            'remarks' => $app->remarks ?? null,
                            'submitted_at' => $app->created_at->format('Y-m-d H:i:s'),
                            'date' => $app->created_at->format('Y-m-d'),
                            'created_at' => $app->created_at->format('M d, Y'),
                            'sort_date' => $app->created_at,
                            'updated_at' => $app->updated_at
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Could not fetch supply request applications: ' . $e->getMessage());
            }

            // FETCH FISHR APPLICATIONS
            try {
                if (class_exists('App\Models\FishrApplication')) {
                    $fishrApps = FishrApplication::where('user_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->get();

                    foreach ($fishrApps as $app) {
                        $allApplications[] = [
                            'id' => $app->id,
                            'type' => 'FishR Registration',
                            'application_number' => $app->registration_number ?? 'FR-' . $app->id,
                            'reference_number' => $app->registration_number ?? 'FR-' . $app->id,
                            'status' => $app->status,
                            'description' => 'Fisherfolk registration',
                            'full_name' => $app->full_name ?? $app->name ?? 'N/A',
                            'barangay' => $app->barangay ?? null,
                            'remarks' => $app->remarks ?? null,
                            'submitted_at' => $app->created_at->format('Y-m-d H:i:s'),
                            'date' => $app->created_at->format('Y-m-d'),
                            'created_at' => $app->created_at->format('M d, Y'),
                            'sort_date' => $app->created_at,
                            'updated_at' => $app->updated_at
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Could not fetch FishR applications: ' . $e->getMessage());
            }

            // FETCH BOATR APPLICATIONS
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
                            'sort_date' => $app->created_at,
                            'updated_at' => $app->updated_at
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Could not fetch BoatR applications: ' . $e->getMessage());
            }

            // FETCH TRAINING APPLICATIONS
            try {
                if (class_exists('App\Models\TrainingApplication')) {
                    $trainingApps = TrainingApplication::where('user_id', $userId)
                        ->orderBy('created_at', 'desc')
                        ->get();

                    foreach ($trainingApps as $app) {
                        $allApplications[] = [
                            'id' => $app->id,
                            'type' => 'Training Request',
                            'application_number' => $app->application_number ?? 'TR-' . $app->id,
                            'reference_number' => $app->application_number ?? 'TR-' . $app->id,
                            'status' => $app->status,
                            'description' => 'Agricultural training program: ' . ($app->training_type ?? 'General'),
                            'full_name' => $app->full_name ?? ($app->first_name . ' ' . $app->last_name),
                            'training_type' => $app->training_type,
                            'barangay' => $app->barangay ?? null,
                            'remarks' => $app->remarks ?? null,
                            'submitted_at' => $app->created_at->format('Y-m-d H:i:s'),
                            'date' => $app->created_at->format('Y-m-d'),
                            'created_at' => $app->created_at->format('M d, Y'),
                            'sort_date' => $app->created_at,
                            'updated_at' => $app->updated_at
                        ];
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Could not fetch training applications: ' . $e->getMessage());
            }

            usort($allApplications, function($a, $b) {
                return $b['sort_date'] <=> $a['sort_date'];
            });

            $recentActivity = $this->generateRecentActivity($allApplications);

            $allApplications = array_map(function($app) {
                unset($app['sort_date']);
                unset($app['updated_at']);
                return $app;
            }, $allApplications);

            Log::info('Successfully fetched applications', [
                'user_id' => $userId,
                'total_count' => count($allApplications)
            ]);

            return response()->json([
                'success' => true,
                'applications' => $allApplications,
                'recent_activity' => $recentActivity,
                'total' => count($allApplications),
                'breakdown' => [
                    'rsbsa' => $rsbsaApps->count(),
                    'supplies' => isset($seedlingApps) ? $seedlingApps->count() : 0,
                    'fishr' => isset($fishrApps) ? $fishrApps->count() : 0,
                    'boatr' => isset($boatrApps) ? $boatrApps->count() : 0,
                    'training' => isset($trainingApps) ? $trainingApps->count() : 0,
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

    private function generateRecentActivity($applications)
    {
        $activities = [];

        foreach ($applications as $app) {
            $activities[] = [
                'icon' => $this->getActivityIcon($app['type']),
                'text' => 'Submitted ' . $app['type'],
                'date' => $this->getRelativeTime($app['submitted_at']),
                'timestamp' => $app['submitted_at']
            ];

            if (isset($app['updated_at']) && $app['updated_at'] != $app['sort_date']) {
                $activities[] = [
                    'icon' => $this->getStatusIcon($app['status']),
                    'text' => $app['type'] . ' - ' . ucfirst(str_replace('_', ' ', $app['status'])),
                    'date' => $this->getRelativeTime($app['updated_at']),
                    'timestamp' => $app['updated_at']
                ];
            }
        }

        usort($activities, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        $activities = array_map(function($activity) {
            unset($activity['timestamp']);
            return $activity;
        }, array_slice($activities, 0, 5));

        return $activities;
    }

    private function getActivityIcon($type)
    {
        $icons = [
            'RSBSA Registration' => '📋',
            'Seedlings Request' => '🌱',
            'FishR Registration' => '🐟',
            'BoatR Registration' => '⛵',
            'Training Request' => '📚'
        ];
        return $icons[$type] ?? '📄';
    }

    private function getStatusIcon($status)
    {
        $icons = [
            'pending' => '⏳',
            'under_review' => '🔍',
            'approved' => '✅',
            'rejected' => '❌',
            'completed' => '✅'
        ];
        return $icons[strtolower($status)] ?? '📌';
    }

    private function getRelativeTime($timestamp)
    {
        $time = strtotime($timestamp);
        $now = time();
        $diff = $now - $time;

        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 2592000) {
            $weeks = floor($diff / 604800);
            return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
        } else {
            $months = floor($diff / 2592000);
            return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
        }
    }

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

    public function getTrainingApplications(Request $request)
    {
        try {
            $userId = session('user.id');

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in to view your applications'
                ], 401);
            }

            $applications = TrainingApplication::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($app) {
                    return [
                        'id' => $app->id,
                        'type' => 'Training Request',
                        'application_number' => $app->application_number ?? 'TR-' . $app->id,
                        'status' => $app->status,
                        'description' => 'Agricultural training program: ' . ($app->training_type ?? 'General'),
                        'full_name' => $app->full_name ?? ($app->first_name . ' ' . $app->last_name),
                        'training_type' => $app->training_type,
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
            Log::error('Error fetching training applications: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load training applications'
            ], 500);
        }
    }

    public function getApplicationStats(Request $request)
    {
        try {
            $userId = session('user.id');

            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in'
                ], 401);
            }

            $total =
                RsbsaApplication::where('user_id', $userId)->count() +
                SeedlingRequest::where('user_id', $userId)->count() +
                FishrApplication::where('user_id', $userId)->count() +
                BoatrApplication::where('user_id', $userId)->count() +
                TrainingApplication::where('user_id', $userId)->count();

            $approved =
                RsbsaApplication::where('user_id', $userId)->where('status', 'approved')->count() +
                SeedlingRequest::where('user_id', $userId)->where('status', 'approved')->count() +
                FishrApplication::where('user_id', $userId)->where('status', 'approved')->count() +
                BoatrApplication::where('user_id', $userId)->where('status', 'approved')->count() +
                TrainingApplication::where('user_id', $userId)->where('status', 'approved')->count();

            $pending =
                RsbsaApplication::where('user_id', $userId)->whereIn('status', ['pending', 'under_review'])->count() +
                SeedlingRequest::where('user_id', $userId)->whereIn('status', ['pending', 'under_review'])->count() +
                FishrApplication::where('user_id', $userId)->whereIn('status', ['pending', 'under_review'])->count() +
                BoatrApplication::where('user_id', $userId)->whereIn('status', ['pending', 'under_review'])->count() +
                TrainingApplication::where('user_id', $userId)->whereIn('status', ['pending', 'under_review'])->count();

            return response()->json([
                'success' => true,
                'total' => $total,
                'approved' => $approved,
                'pending' => $pending
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching application stats: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to load statistics'
            ], 500);
        }
    }
}
