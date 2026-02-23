<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use App\Models\UserRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Display activity logs - Admin and Superadmin
     */
    public function index(Request $request)
    {
        // Only admin and superadmin can view activity logs
        if (!Auth::check() || !Auth::user()->hasAdminPrivileges()) {
            abort(403, 'Unauthorized access to activity logs');
        }

        try {
            $query = Activity::with(['causer', 'subject'])->latest();

            // Exclude system-generated technical records (ItemSupplyLog)
            $query->where(function($q) {
                $q->whereNull('subject_type')
                  ->orWhere('subject_type', 'not like', '%ItemSupplyLog%');
            });

            // Role-based filtering: Admin sees only admin activities, Superadmin sees everything
            if (Auth::user()->isAdmin()) {
                // Show activities from admin Users OR activities about UserRegistration
                $query->where(function($q) {
                    $q->where(function($q2) {
                        // Admin-caused activities
                        $q2->where('causer_type', 'App\\Models\\User')
                           ->whereExists(function($q3) {
                               $q3->selectRaw('1')
                                 ->from('users')
                                 ->whereColumn('users.id', 'activity_log.causer_id')
                                 ->where('users.role', 'admin');
                           });
                    })
                    ->orWhere(function($q2) {
                        // Activities about UserRegistration (registrations being approved/rejected)
                        $q2->where('subject_type', 'App\\Models\\UserRegistration');
                    })
                    ->orWhere(function($q2) {
                        // UserRegistration login/logout activities
                        $q2->where('causer_type', 'App\\Models\\UserRegistration');
                    });
                });
            }
            // Superadmin sees all activities (no additional filter)

            // Search by description
            if ($request->filled('search')) {
                $query->where('description', 'like', '%' . $request->search . '%');
            }

            // Filter by event type
            if ($request->filled('event')) {
                $event = $request->event;
                if ($event === 'login_admin') {
                    $query->where('event', 'login')->where('causer_type', 'App\\Models\\User');
                } elseif ($event === 'logout_admin') {
                    $query->where('event', 'logout')->where('causer_type', 'App\\Models\\User');
                } elseif ($event === 'login_user') {
                    $query->where('event', 'login')->where('causer_type', 'App\\Models\\UserRegistration');
                } elseif ($event === 'logout_user') {
                    $query->where('event', 'logout')->where('causer_type', 'App\\Models\\UserRegistration');
                } elseif ($event === 'status_changed_approved') {
                    $query->where('event', 'status_changed')
                          ->where('properties', 'like', '%"new_status":"approved"%');
                } elseif ($event === 'status_changed_rejected') {
                    $query->where('event', 'status_changed')
                          ->where('properties', 'like', '%"new_status":"rejected"%');
                } else {
                    $query->where(function($q) use ($event) {
                        $q->where('event', $event)
                          ->orWhere('description', 'like', $event . '%')
                          ->orWhere('description', 'like', '%' . $event . ' -%');
                    });
                }
            }

            // Filter by actor role or service module
            if ($request->filled('module')) {
                $module = $request->module;
                if ($module === 'role_admin') {
                    $query->where('causer_type', 'App\\Models\\User')
                          ->whereExists(function($q) {
                              $q->selectRaw('1')->from('users')
                                ->whereColumn('users.id', 'activity_log.causer_id')
                                ->where('users.role', 'admin');
                          });
                } elseif ($module === 'role_superadmin') {
                    $query->where('causer_type', 'App\\Models\\User')
                          ->whereExists(function($q) {
                              $q->selectRaw('1')->from('users')
                                ->whereColumn('users.id', 'activity_log.causer_id')
                                ->where('users.role', 'superadmin');
                          });
                } elseif ($module === 'role_user') {
                    $query->where('causer_type', 'App\\Models\\UserRegistration');
                } elseif ($module === 'Supply') {
                    $query->where(function($q) {
                        $q->where('subject_type', 'like', '%SeedlingRequest%')
                          ->orWhere('subject_type', 'like', '%CategoryItem%');
                    });
                } elseif ($module === 'DSSReport') {
                    $query->where(function($q) {
                        $q->where('event', 'dss_report_viewed')
                          ->orWhere('event', 'dss_report_downloaded')
                          ->orWhere('description', 'like', '%DSS Report%');
                    });
                } else {
                    $query->where('subject_type', 'like', '%' . $module . '%');
                }
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $activities = $query->paginate(10);

            return view('admin.activity-logs.index', compact('activities'));

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show activity details via AJAX
     */
    public function show($id)
    {
        if (!Auth::check() || !Auth::user()->hasAdminPrivileges()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            // Role-based access: Admin can view admin activities and UserRegistration activities
            if (Auth::user()->isAdmin()) {
                $activity = Activity::with(['causer', 'subject'])
                    ->where(function($q) {
                        $q->where(function($q2) {
                            // Admin-caused activities
                            $q2->where('causer_type', 'App\\Models\\User')
                               ->whereExists(function($q3) {
                                   $q3->selectRaw('1')
                                     ->from('users')
                                     ->whereColumn('users.id', 'activity_log.causer_id')
                                     ->where('users.role', 'admin');
                               });
                        })
                        ->orWhere(function($q2) {
                            // Activities about UserRegistration
                            $q2->where('subject_type', 'App\\Models\\UserRegistration');
                        })
                        ->orWhere(function($q2) {
                            // UserRegistration login/logout activities
                            $q2->where('causer_type', 'App\\Models\\UserRegistration');
                        });
                    })->find($id);

                if (!$activity) {
                    return response()->json(['error' => 'Not found or unauthorized'], 403);
                }
            } else {
                // Superadmin can view all activities
                $activity = Activity::with(['causer', 'subject'])->findOrFail($id);
            }

            // Get user who performed the action
            $user = $activity->causer;
            $userName = 'System';
            $userEmail = 'N/A';
            $userRole = 'System';

            if ($user) {
                // Check if it's a UserRegistration or User model
                if ($user instanceof \App\Models\UserRegistration) {
                    $userName = $user->username ?? 'Unknown User';
                    $userEmail = ucfirst($user->user_type ?? 'Portal User');
                    $userRole = ucfirst($user->user_type ?? 'user');
                } else {
                    $userName = $user->name;
                    $userEmail = $user->email;
                    $userRole = ucfirst($user->role ?? 'user');
                }
            } elseif (in_array($activity->event, ['login', 'logout', 'login_failed'])) {
                // For login/logout without causer, check properties
                $properties = $activity->properties->all() ?? [];
                $userName = $properties['name'] ?? ($properties['email'] ?? ($properties['username'] ?? 'Unknown User'));
                $userEmail = $properties['email'] ?? ($properties['first_name'] ?? 'N/A');
                $userRole = isset($properties['role']) ? ucfirst($properties['role']) : 'User';
            }

            // Get subject information (what was affected)
            $subject = $activity->subject;
            $subjectName = null;
            $subjectType = null;
            $subjectUserType = null;

            if ($subject) {
                $subjectType = class_basename(get_class($subject));
                if ($subject instanceof \App\Models\UserRegistration) {
                    $subjectName = $subject->username ?? 'Unknown User';
                    $subjectUserType = ucfirst($subject->user_type ?? 'user');
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $activity->id,
                    'date' => $activity->created_at->format('M d, Y h:i A'),
                    'user' => $userName,
                    'email' => $userEmail,
                    'role' => $userRole,
                    'action' => ucfirst($activity->event),
                    'description' => $activity->description,
                    'model' => class_basename($activity->subject_type),
                    'subject_name' => $subjectName, // The UserRegistration username/name being acted upon
                    'subject_type' => $subjectUserType, // The UserRegistration user_type
                    'ip' => $activity->properties['ip_address'] ?? 'N/A',
                    // NEW: Include properties for before/after comparison
                    'properties' => $activity->properties ?? [],
                    'properties_old' => $activity->properties['old'] ?? $activity->properties['attributes'] ?? null,
                    'properties_new' => $activity->properties['attributes'] ?? $activity->properties['old'] ?? null,
                    'has_changes' => isset($activity->properties['changes']) || isset($activity->properties['old']),
                    'changes' => $activity->properties['changes'] ?? null
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Not found'], 404);
        }
    }

    /**
     * Export activity logs to CSV - Admin and Superadmin
     */
    public function export(Request $request)
    {
        if (!Auth::check() || !Auth::user()->hasAdminPrivileges()) {
            abort(403, 'Unauthorized');
        }

        $query = Activity::with('causer')->latest();

        // Exclude system-generated technical records (ItemSupplyLog)
        $query->where(function($q) {
            $q->whereNull('subject_type')
              ->orWhere('subject_type', 'not like', '%ItemSupplyLog%');
        });

        // Role-based filtering: Admin exports only admin activities, Superadmin exports everything
        if (Auth::user()->isAdmin()) {
            // Show activities from admin Users OR activities about UserRegistration
            $query->where(function($q) {
                $q->where(function($q2) {
                    // Admin-caused activities
                    $q2->where('causer_type', 'App\\Models\\User')
                       ->whereExists(function($q3) {
                           $q3->selectRaw('1')
                             ->from('users')
                             ->whereColumn('users.id', 'activity_log.causer_id')
                             ->where('users.role', 'admin');
                       });
                })
                ->orWhere(function($q2) {
                    // Activities about UserRegistration
                    $q2->where('subject_type', 'App\\Models\\UserRegistration');
                })
                ->orWhere(function($q2) {
                    // UserRegistration login/logout activities
                    $q2->where('causer_type', 'App\\Models\\UserRegistration');
                });
            });
        }

        // Search by description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        // Filter by event type
        if ($request->filled('event')) {
            $event = $request->event;
            if ($event === 'login_admin') {
                $query->where('event', 'login')->where('causer_type', 'App\\Models\\User');
            } elseif ($event === 'logout_admin') {
                $query->where('event', 'logout')->where('causer_type', 'App\\Models\\User');
            } elseif ($event === 'login_user') {
                $query->where('event', 'login')->where('causer_type', 'App\\Models\\UserRegistration');
            } elseif ($event === 'logout_user') {
                $query->where('event', 'logout')->where('causer_type', 'App\\Models\\UserRegistration');
            } elseif ($event === 'status_changed_approved') {
                $query->where('event', 'status_changed')
                      ->where('properties', 'like', '%"new_status":"approved"%');
            } elseif ($event === 'status_changed_rejected') {
                $query->where('event', 'status_changed')
                      ->where('properties', 'like', '%"new_status":"rejected"%');
            } else {
                $query->where(function($q) use ($event) {
                    $q->where('event', $event)
                      ->orWhere('description', 'like', $event . '%')
                      ->orWhere('description', 'like', '%' . $event . ' -%');
                });
            }
        }

        // Filter by actor role or service module
        if ($request->filled('module')) {
            $module = $request->module;
            if ($module === 'role_admin') {
                $query->where('causer_type', 'App\\Models\\User')
                      ->whereExists(function($q) {
                          $q->selectRaw('1')->from('users')
                            ->whereColumn('users.id', 'activity_log.causer_id')
                            ->where('users.role', 'admin');
                      });
            } elseif ($module === 'role_superadmin') {
                $query->where('causer_type', 'App\\Models\\User')
                      ->whereExists(function($q) {
                          $q->selectRaw('1')->from('users')
                            ->whereColumn('users.id', 'activity_log.causer_id')
                            ->where('users.role', 'superadmin');
                      });
            } elseif ($module === 'role_user') {
                $query->where('causer_type', 'App\\Models\\UserRegistration');
            } elseif ($module === 'Supply') {
                $query->where(function($q) {
                    $q->where('subject_type', 'like', '%SeedlingRequest%')
                      ->orWhere('subject_type', 'like', '%CategoryItem%');
                });
            } elseif ($module === 'DSSReport') {
                $query->where(function($q) {
                    $q->where('event', 'dss_report_viewed')
                      ->orWhere('event', 'dss_report_downloaded')
                      ->orWhere('description', 'like', '%DSS Report%');
                });
            } else {
                $query->where('subject_type', 'like', '%' . $module . '%');
            }
        }

        // Apply date filters
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->get();

        $filename = 'audit-logs-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');

            // CSV Headers
            fputcsv($file, ['Date', 'User', 'Email', 'Role', 'Action', 'What Changed']);

            foreach ($activities as $activity) {
                $user = $activity->causer;
                $userName = 'System';
                $userEmail = '-';
                $userRole = 'System';

                if ($user) {
                    // Check if it's a UserRegistration or User model
                    if ($user instanceof \App\Models\UserRegistration) {
                        $userName = $user->username ?? 'Unknown User';
                        $userEmail = ucfirst($user->user_type ?? 'Portal User'); // shows "Farmer", "Fisherfolk", etc.
                        $userRole = ucfirst($user->user_type ?? 'user');
                    } else {
                        $userName = $user->name;
                        $userEmail = $user->email;
                        $userRole = ucfirst($user->role ?? 'user');
                    }
                } elseif (in_array($activity->event, ['login', 'logout', 'login_failed'])) {
                    // For login/logout without causer, check properties
                    $properties = $activity->properties->all() ?? [];
                    $userName = $properties['name'] ?? ($properties['email'] ?? ($properties['username'] ?? 'Unknown User'));
                    $userEmail = $properties['email'] ?? ($properties['first_name'] ?? '-');
                    $userRole = isset($properties['role']) ? ucfirst($properties['role']) : 'User';
                }

                fputcsv($file, [
                    $activity->created_at->format('Y-m-d H:i:s'),
                    $userName,
                    $userEmail,
                    $userRole,
                    ucfirst($activity->event),
                    $activity->description
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
