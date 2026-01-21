<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    /**
     * Display all events with statistics (Admin View)
     */
    public function index(Request $request)
    {
        $query = Event::with(['creator', 'updater'])->notArchived()->ordered();

        if ($request->has('category') && !empty($request->category) && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->has('date_from') && !empty($request->date_from)) {
            $dateFrom = $request->date_from;
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $dateTo = $request->date_to . ' 23:59:59';
            $query->where('created_at', '<=', $dateTo);
        }

        $events = $query->orderBy('created_at', 'DESC')
                    ->paginate(10);

        $stats = [
            'total' => Event::notArchived()->count(),
            'active' => Event::active()->count(),
            'inactive' => Event::notArchived()->where('is_active', false)->count(),
            'archived' => Event::archived()->count(),
            'announcements' => Event::active()->where('category', 'announcement')->count(),
            'ongoing' => Event::active()->where('category', 'ongoing')->count(),
            'upcoming' => Event::active()->where('category', 'upcoming')->count(),
            'past' => Event::active()->where('category', 'past')->count(),
        ];

        return view('admin.event.index', compact('events', 'stats'));
    }

    /**
     * Get all ACTIVE events as JSON (PUBLIC API ENDPOINT)
     */
    public function getEvents(Request $request)
    {
        try {
            \Log::info(' Events API called', [
                'category' => $request->get('category', 'all'),
                'ip' => $request->ip(),
            ]);

            $query = Event::active();

            $query->orderBy('display_order', 'asc')
                  ->orderBy('created_at', 'desc');

            if ($request->has('category') && $request->category !== 'all') {
                $query->where('category', $request->category);
            }

            $events = $query->get();

            \Log::info(' Events retrieved', [
                'count' => $events->count(),
                'categories' => $events->pluck('category')->unique()->values()
            ]);

            $formattedEvents = $events->map(function($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'short_description' => \Str::limit($event->description, 120),
                    'category' => $event->category,
                    'category_label' => ucfirst($event->category),
                    'image' => $event->image_url,
                    'image_path' => $event->image_path,
                    'date' => $event->date ?? 'Date TBA',
                    'location' => $event->location ?? 'Location TBA',
                    'details' => $event->details ?? [],
                    'is_active' => (bool) $event->is_active,
                    'display_order' => $event->display_order,
                    'formatted_date' => $event->formatted_date,
                    'created_at' => $event->created_at->toIso8601String(),
                    'created_at_human' => $event->created_at->diffForHumans(),
                ];
            });

            return response()->json([
                'success' => true,
                'events' => $formattedEvents,
                'count' => $formattedEvents->count(),
                'timestamp' => now()->toIso8601String()
            ], 200);

        } catch (\Exception $e) {
            \Log::error(' Events API Error', [
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load events',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred',
                'timestamp' => now()->toIso8601String()
            ], 500);
        }
    }

    /**
     * Store a new event
     * NEW LOGIC:
     * - Only 1 ACTIVE event per category
     * - NEW events default to INACTIVE status
     * - Announcements/Ongoing/Upcoming/Past categories have 1 default active by default
     * - User must manually activate new events if needed
     */
    public function store(Request $request)
    {
        \Log::info('📥 [Events] Store request received', [
            'category' => $request->category,
            'has_image' => $request->hasFile('image')
        ]);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:events,title',
            'description' => 'required|string|min:10',
            'category' => 'required|string|in:announcement,ongoing,upcoming,past',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'date' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:500',
            'details' => 'nullable|string',
            'is_active' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // NEW LOGIC: All new events default to INACTIVE
            // Only if explicitly requested and not at category limit, can be active
            $isActiveRequest = false;
            
            // Check if user explicitly wants this event active
            if ($request->boolean('is_active', false)) {
                // Check if this category already has 1 active event
                $activeCountInCategory = Event::where('category', $request->category)
                    ->active()
                    ->notArchived()
                    ->count();

                // Only allow activation if no active event exists in this category
                if ($activeCountInCategory < 1) {
                    $isActiveRequest = true;
                } else {
                    // Cannot create as active - category already has an active event
                    return response()->json([
                        'success' => false,
                        'message' => 'The ' . ucfirst($request->category) . ' category already has 1 active event. Please deactivate it first if you want to activate this new event.',
                        'warning_type' => 'category_active_event_exists'
                    ], 422);
                }
            }

            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . \Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs('events', $filename, 'public');
            }

            $maxOrder = Event::notArchived()->max('display_order') ?? -1;

            $details = [];
            if ($request->has('details')) {
                $detailsInput = $request->input('details');
                if (is_string($detailsInput)) {
                    $details = json_decode($detailsInput, true) ?? [];
                } else {
                    $details = $detailsInput;
                }
            }

            $event = Event::create([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'category_label' => ucfirst($request->category),
                'image_path' => $imagePath,
                'date' => $request->date,
                'location' => $request->location,
                'details' => $details,
                'is_active' => $isActiveRequest,
                'is_archived' => false,
                'display_order' => $maxOrder + 1,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);

            DB::commit();

            \Log::info(' [Events] Event created successfully', [
                'id' => $event->id,
                'title' => $event->title,
                'category' => $event->category,
                'is_active' => $event->is_active
            ]);

            // Build success message
            $message = 'Event "' . $event->title . '" created successfully';
            if (!$event->is_active) {
                $message .= ' (set to inactive - you can activate it later)';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'event' => $event->load('creator'),
                'category' => $event->category
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error(' [Events] Failed to create event', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create event',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get a single event with full details
     */
    public function show(Event $event)
    {
        try {
            $event->load(['creator', 'updater', 'archivist']);

            return response()->json([
                'success' => true,
                'event' => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'category' => $event->category,
                    'category_label' => ucfirst($event->category),
                    'image' => $event->image_url,
                    'image_path' => $event->image_path,
                    'date' => $event->date,
                    'location' => $event->location,
                    'details' => $event->details ?? [],
                    'is_active' => (bool) $event->is_active,
                    'is_archived' => (bool) $event->is_archived,
                    'archived_at' => $event->archived_at,
                    'archive_reason' => $event->archive_reason,
                    'display_order' => $event->display_order,
                    'created_at' => $event->created_at->toIso8601String(),
                    'updated_at' => $event->updated_at->toIso8601String(),
                    'creator' => $event->creator ? [
                        'id' => $event->creator->id,
                        'name' => $event->creator->name,
                    ] : null,
                    'updater' => $event->updater ? [
                        'id' => $event->updater->id,
                        'name' => $event->updater->name,
                    ] : null,
                    'archivist' => $event->archivist ? [
                        'id' => $event->archivist->id,
                        'name' => $event->archivist->name,
                    ] : null,
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error(' [Events] Failed to load event', [
                'id' => $event->id ?? 'unknown',
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Event not found or failed to load'
            ], 404);
        }
    }

    /**
     * Update an event
     */
    public function update(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:events,title,' . $event->id,
            'description' => 'required|string|min:10',
            'category' => 'required|string|in:announcement,ongoing,upcoming,past',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'date' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:500',
            'details' => 'nullable|string',
            'is_active' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // NEW LOGIC: Allow updating status freely per new rules
            // No restrictions on deactivation in edit form
            $isActiveRequest = $request->boolean('is_active', $event->is_active);

            // If changing to active, check 1 per category limit
            if (!$event->is_active && $isActiveRequest) {
                $activeCountInCategory = Event::where('category', $event->category)
                    ->active()
                    ->notArchived()
                    ->where('id', '!=', $event->id)
                    ->count();

                if ($activeCountInCategory >= 1) {
                    return response()->json([
                        'success' => false,
                        'message' => 'The ' . ucfirst($event->category) . ' category already has 1 active event. Please deactivate it first.',
                        'warning_type' => 'only_one_active_allowed'
                    ], 422);
                }
            }

            $newImagePath = $event->image_path;
            if ($request->hasFile('image')) {
                if ($event->image_path && Storage::disk('public')->exists($event->image_path)) {
                    Storage::disk('public')->delete($event->image_path);
                }

                $file = $request->file('image');
                $filename = time() . '_' . \Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
                $newImagePath = $file->storeAs('events', $filename, 'public');
            }

            $details = [];
            if ($request->has('details')) {
                $detailsInput = $request->input('details');
                if (is_string($detailsInput)) {
                    $details = json_decode($detailsInput, true) ?? [];
                } else {
                    $details = $detailsInput;
                }
            }

            $event->update([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'category_label' => ucfirst($request->category),
                'image_path' => $newImagePath,
                'date' => $request->date,
                'location' => $request->location,
                'details' => $details,
                'is_active' => $isActiveRequest,
                'updated_by' => auth()->id()
            ]);

            DB::commit();

            \Log::info(' [Events] Event updated', ['id' => $event->id]);

            return response()->json([
                'success' => true,
                'message' => 'Event "' . $event->title . '" updated successfully',
                'event' => $event->fresh()->load('creator', 'updater')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error(' [Events] Failed to update event', [
                'id' => $event->id,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update event'
            ], 500);
        }
    }

    /**
     * Archive an event
     * SAFETY: Prevents archiving last active event
     */
    public function archive(Request $request, Event $event)
    {
        try {
            // SAFETY CHECK: Cannot archive if event is active
            if ($event->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot archive an active event. Please deactivate it first by clicking the Toggle button.',
                    'warning_type' => 'event_is_active'
                ], 422);
            }

            DB::beginTransaction();

            $reason = $request->input('reason', null);

            $event->archive(auth()->id(), $reason);

            DB::commit();

            \Log::info(' [Events] Event archived', ['id' => $event->id, 'reason' => $reason]);

            return response()->json([
                'success' => true,
                'message' => 'Event "' . $event->title . '" has been archived'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error(' [Events] Failed to archive event', [
                'id' => $event->id,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to archive event'
            ], 500);
        }
    }

    /**
     * Restore/unarchive an event
     */
    public function unarchive(Event $event)
    {
        try {
            DB::beginTransaction();

            $event->unarchive(auth()->id());

            DB::commit();

            \Log::info(' [Events] Event restored', ['id' => $event->id]);

            return response()->json([
                'success' => true,
                'message' => 'Event "' . $event->title . '" has been restored (remains inactive)'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error(' [Events] Failed to restore event', [
                'id' => $event->id,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to restore event'
            ], 500);
        }
    }

    /**
     * Permanently delete an event
     * SAFETY: Prevents deleting last active event
     */
    public function destroy(Event $event)
    {
        try {
            // SAFETY CHECK: Don't delete if event is active
            if ($event->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete an active event. Please deactivate it first by clicking the Toggle button.',
                    'warning_type' => 'event_is_active'
                ], 422);
            }

            DB::beginTransaction();

            $eventTitle = $event->title;
            $eventId = $event->id;

            if ($event->image_path && Storage::disk('public')->exists($event->image_path)) {
                Storage::disk('public')->delete($event->image_path);
            }

            $event->forceDelete();

            DB::commit();

            \Log::info(' [Events] Event permanently deleted', ['id' => $eventId, 'title' => $eventTitle]);

            return response()->json([
                'success' => true,
                'message' => 'Event "' . $eventTitle . '" has been permanently deleted'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error(' [Events] Failed to delete event', [
                'id' => $event->id,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete event'
            ], 500);
        }
    }

    /**
     * Toggle event active status
     * NEW LOGIC:
     * - Only 1 ACTIVE event per category (maximum)
     * - Cannot activate if category already has 1 active (must deactivate that one first)
     * - Can deactivate any event (even if it's the only active in category)
     * - Provides clear notification when trying to activate
     */
    public function toggleStatus(Event $event)
    {
        try {
            $newStatus = !$event->is_active;

            // If trying to ACTIVATE
            if (!$event->is_active && $newStatus) {
                // Check if this category already has an active event
                $activeCountInCategory = Event::where('category', $event->category)
                    ->active()
                    ->notArchived()
                    ->count();

                if ($activeCountInCategory >= 1) {
                    // Get the active event details for the notification
                    $activeEvent = Event::where('category', $event->category)
                        ->active()
                        ->notArchived()
                        ->first();

                    return response()->json([
                        'success' => false,
                        'message' => 'The ' . ucfirst($event->category) . ' category already has 1 active event: "' . $activeEvent->title . '". Please deactivate it first if you want to activate this event.',
                        'warning_type' => 'only_one_active_allowed',
                        'active_event' => [
                            'id' => $activeEvent->id,
                            'title' => $activeEvent->title
                        ]
                    ], 422);
                }
            }

            // All checks passed - update status
            $event->update([
                'is_active' => $newStatus,
                'updated_by' => auth()->id()
            ]);

            \Log::info(' [Events] Event status toggled', [
                'id' => $event->id,
                'category' => $event->category,
                'new_status' => $newStatus ? 'active' : 'inactive'
            ]);

            return response()->json([
                'success' => true,
                'message' => $newStatus
                    ? ' Event "' . $event->title . '" is now active'
                    : ' Event "' . $event->title . '" is now inactive',
                'is_active' => $newStatus,
                'category' => $event->category
            ]);

        } catch (\Exception $e) {
            \Log::error(' [Events] Failed to toggle event status', [
                'id' => $event->id,
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    /**
     * Get archived events for archive management view
     */
    public function archivedEvents(Request $request)
    {
        try {
            $query = Event::archived()
                ->with(['creator', 'updater', 'archivist'])
                ->orderBy('archived_at', 'desc');

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $events = $query->paginate(20);

            return view('admin.event.archived', compact('events'));

        } catch (\Exception $e) {
            \Log::error(' [Events] Failed to fetch archived events', [
                'message' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'Failed to fetch archived events');
        }
    }

    /**
     * Get comprehensive event statistics
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total' => Event::notArchived()->count(),
                'active' => Event::active()->count(),
                'inactive' => Event::notArchived()->where('is_active', false)->count(),
                'archived' => Event::archived()->count(),
                'announcements' => Event::active()->where('category', 'announcement')->count(),
                'ongoing' => Event::active()->where('category', 'ongoing')->count(),
                'upcoming' => Event::active()->where('category', 'upcoming')->count(),
                'past' => Event::active()->where('category', 'past')->count(),
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'timestamp' => now()->toIso8601String()
            ]);

        } catch (\Exception $e) {
            \Log::error(' [Events] Failed to fetch statistics', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics'
            ], 500);
        }
    }
}