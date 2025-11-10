<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventLog;
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

        // Date filtering with proper handling
        if ($request->has('date_from') && !empty($request->date_from)) {
            $dateFrom = $request->date_from;
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $dateTo = $request->date_to . ' 23:59:59';
            $query->where('created_at', '<=', $dateTo);
        }

        // $events = $query->paginate(15)->withQueryString();
        // SORT BY NEWEST FIRST (newest created_at at top)
        $events = $query->orderBy('created_at', 'DESC')
                    ->paginate(15);

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
            \Log::info('📡 Events API called', [
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
            
            \Log::info('✅ Events retrieved', [
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
            \Log::error('❌ Events API Error', [
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
     * SAFETY: Auto-activates if no active events exist
     * AUTO-DEACTIVATES: Old events in same category when new active event created
     */
    public function store(Request $request)
    {
        \Log::info('📥 [Events] Store request received', [
            'method' => $request->method(),
            'all_data' => $request->all(),
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
        ], [
            'title.required' => 'Event title is required',
            'title.unique' => 'An event with this title already exists',
            'description.required' => 'Event description is required',
            'description.min' => 'Description must be at least 10 characters',
            'category.required' => 'Please select an event category',
            'category.in' => 'Invalid category selected',
            'image.max' => 'Image size must not exceed 5MB',
            'image.image' => 'File must be a valid image',
            'image.mimes' => 'Image must be JPEG, PNG, GIF, or WebP'
        ]);

        if ($validator->fails()) {
            \Log::error('❌ [Events] Validation failed', [
                'errors' => $validator->errors()->toArray(),
                'title_length' => strlen($request->title ?? ''),
                'description_length' => strlen($request->description ?? ''),
                'description_value' => substr($request->description ?? '', 0, 50)
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all()),
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // AUTO-ACTIVATION & AUTO-DEACTIVATION LOGIC
            $activeCount = Event::active()->count();
            $isActiveRequest = $request->boolean('is_active', true);
            $wasForced = false;
            $wasDeactivated = false;
            $deactivatedCount = 0;

            // If no active events exist, force this one to be active
            if ($activeCount === 0) {
                $isActiveRequest = true;
                $wasForced = true;
                
                \Log::info('⚠️ [Events] No active events exist - forcing new event to be active', [
                    'user_id' => auth()->id()
                ]);
            }

            // If creating an active event, auto-deactivate old ones in same category
            if ($isActiveRequest && $activeCount > 0) {
                $deactivatedCount = Event::where('category', $request->category)
                    ->where('is_active', true)
                    ->where('is_archived', false)
                    ->update([
                        'is_active' => false,
                        'updated_by' => auth()->id(),
                        'updated_at' => now()
                    ]);
                
                $wasDeactivated = $deactivatedCount > 0;

                \Log::info('✅ [Events] Auto-deactivated old events in category', [
                    'category' => $request->category,
                    'count' => $deactivatedCount,
                    'user_id' => auth()->id()
                ]);
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

            $event->logAction('created', auth()->id(), null, 'Event created successfully');

            DB::commit();

            \Log::info('✅ [Events] Event created successfully', [
                'id' => $event->id, 
                'title' => $event->title,
                'category' => $event->category,
                'is_active' => $event->is_active,
                'was_forced_active' => $wasForced,
                'old_events_deactivated' => $deactivatedCount
            ]);

            // Build success message
            $message = 'Event "' . $event->title . '" created successfully';
            if ($wasForced) {
                $message .= ' and set as active (no other active events existed)';
            }
            if ($wasDeactivated) {
                $message .= '. Previous events in ' . ucfirst($request->category) . ' category have been automatically deactivated.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'event' => $event->load('creator'),
                'auto_deactivated' => $wasDeactivated,
                'was_forced_active' => $wasForced,
                'deactivated_count' => $deactivatedCount,
                'category' => $event->category
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('❌ [Events] Failed to create event', [
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
            $event->load(['creator', 'updater', 'archivist', 'logs.performer']);
            
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
                    'logs' => $event->logs->map(function($log) {
                        return [
                            'id' => $log->id,
                            'action' => $log->action,
                            'performer' => $log->performer_name,
                            'changes' => $log->formatted_changes,
                            'notes' => $log->notes,
                            'created_at' => $log->created_at->toIso8601String(),
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ [Events] Failed to load event', [
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

            // SAFETY CHECK: Prevent deactivating last active event
            $isActiveRequest = $request->boolean('is_active', $event->is_active);
            $activeCount = Event::active()->count();
            
            if ($event->is_active && !$isActiveRequest && $activeCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => '⚠️ Cannot deactivate the last active event. The landing page requires at least one active event to display.',
                    'warning_type' => 'last_active_event'
                ], 422);
            }

            $changes = [];
            $oldData = $event->only(['title', 'description', 'category', 'date', 'location', 'is_active']);

            $newImagePath = $event->image_path;
            if ($request->hasFile('image')) {
                if ($event->image_path && Storage::disk('public')->exists($event->image_path)) {
                    Storage::disk('public')->delete($event->image_path);
                }
                
                $file = $request->file('image');
                $filename = time() . '_' . \Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
                $newImagePath = $file->storeAs('events', $filename, 'public');
                
                $changes['image'] = ['old' => $event->image_path, 'new' => $newImagePath];
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

            $newData = $event->only(['title', 'description', 'category', 'date', 'location', 'is_active']);
            foreach ($newData as $key => $value) {
                if ($oldData[$key] !== $value) {
                    $changes[$key] = ['old' => $oldData[$key], 'new' => $value];
                }
            }

            if (!empty($changes)) {
                $event->logAction('updated', auth()->id(), $changes, 'Event updated');
            }

            DB::commit();

            \Log::info('✅ [Events] Event updated', ['id' => $event->id, 'changes' => count($changes)]);

            return response()->json([
                'success' => true,
                'message' => 'Event "' . $event->title . '" updated successfully',
                'event' => $event->fresh()->load('creator', 'updater'),
                'changes_count' => count($changes)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('❌ [Events] Failed to update event', [
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
            // SAFETY CHECK: Don't archive if it's the last active event
            $activeCount = Event::active()->count();
            if ($activeCount <= 1 && $event->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'This is the only active event on your landing page. Please create or activate another event before archiving this one to ensure visitors always have content to view.',
                    'warning_type' => 'last_active_event'
                ], 422);
            }

            DB::beginTransaction();

            $reason = $request->input('reason', null);

            $event->archive(auth()->id(), $reason);

            DB::commit();

            \Log::info('✅ [Events] Event archived', ['id' => $event->id, 'reason' => $reason]);

            return response()->json([
                'success' => true,
                'message' => 'Event "' . $event->title . '" has been archived'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('❌ [Events] Failed to archive event', [
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

            \Log::info('✅ [Events] Event restored', ['id' => $event->id]);

            return response()->json([
                'success' => true,
                'message' => 'Event "' . $event->title . '" has been restored'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('❌ [Events] Failed to restore event', [
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
           // SAFETY CHECK: Don't delete if it's the last active event
            $activeCount = Event::active()->count();
            if ($activeCount <= 1 && $event->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'This is the only active event displayed on your landing page. Please create or activate another event before deleting this one.',
                    'warning_type' => 'last_active_event'
                ], 422);
            }

            DB::beginTransaction();

            $eventTitle = $event->title;
            $eventId = $event->id;

            $event->logAction('deleted', auth()->id(), null, 'Event permanently deleted by ' . auth()->user()->name);

            if ($event->image_path && Storage::disk('public')->exists($event->image_path)) {
                Storage::disk('public')->delete($event->image_path);
            }

            $event->forceDelete();

            DB::commit();

            \Log::info('✅ [Events] Event permanently deleted', ['id' => $eventId, 'title' => $eventTitle]);

            return response()->json([
                'success' => true,
                'message' => 'Event "' . $eventTitle . '" has been permanently deleted'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('❌ [Events] Failed to delete event', [
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
     * SAFETY: Prevents deactivating last active event
     */
    public function toggleStatus(Event $event)
    {
        try {
            $newStatus = !$event->is_active;
            
            // SAFETY CHECK: Prevent deactivating last active event
            $activeCount = Event::active()->count();
            if ($event->is_active && $activeCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'This is the only active event on your landing page. Please activate or create another event before turning this one off.',
                    'warning_type' => 'last_active_event'
                ], 422);
            }

            $event->update([
                'is_active' => $newStatus,
                'updated_by' => auth()->id()
            ]);

            $action = $newStatus ? 'published' : 'unpublished';
            $event->logAction($action, auth()->id(), [
                'is_active' => ['old' => !$newStatus, 'new' => $newStatus]
            ], 'Event status changed');

            \Log::info('✅ [Events] Event status toggled', [
                'id' => $event->id,
                'new_status' => $newStatus ? 'active' : 'inactive'
            ]);

            return response()->json([
                'success' => true,
                'message' => $newStatus 
                    ? 'Event "' . $event->title . '" is now active' 
                    : 'Event "' . $event->title . '" is now inactive',
                'is_active' => $newStatus
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ [Events] Failed to toggle event status', [
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
            \Log::error('❌ [Events] Failed to fetch archived events', [
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
            \Log::error('❌ [Events] Failed to fetch statistics', [
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics'
            ], 500);
        }
    }
}