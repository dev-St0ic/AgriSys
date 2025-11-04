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
        $query = Event::with(['creator', 'updater'])->ordered();

        // Apply category filter
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // Apply search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $events = $query->paginate(15)->withQueryString();

        // Enhanced statistics with more metrics
        $stats = [
            'total' => Event::count(),
            'active' => Event::where('is_active', true)->count(),
            'announcements' => Event::where('category', 'announcement')->count(),
            'ongoing' => Event::where('category', 'ongoing')->count(),
            'upcoming' => Event::where('category', 'upcoming')->count(),
            'past' => Event::where('category', 'past')->count(),
            'inactive' => Event::where('is_active', false)->count(),
            'recent_changes' => EventLog::latest()->take(5)->count()
        ];

        return view('admin.event.index', compact('events', 'stats'));
    }

    /**
     * Get all events as JSON (PUBLIC API ENDPOINT)
     * Used by frontend to display events on landing page
     */
    public function getEvents(Request $request)
    {
        try {
            \Log::info('📡 Events API called', [
                'category' => $request->get('category', 'all'),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            $query = Event::query();
            
            // Only show active events for public API
            $query->where('is_active', true);
            
            // Order by display_order and created_at
            $query->orderBy('display_order', 'asc')
                  ->orderBy('created_at', 'desc');

            // Filter by category if specified
            if ($request->has('category') && $request->category !== 'all') {
                $query->where('category', $request->category);
            }

            $events = $query->get();
            
            \Log::info('✅ Events retrieved successfully', [
                'count' => $events->count(),
                'categories' => $events->pluck('category')->unique()->values()
            ]);

            // Format events for frontend with enhanced data
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
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
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
     * Store a new event with enhanced validation
     */
    public function store(Request $request)
    {
        // Custom validation with better error messages
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:events,title',
            'description' => 'required|string|min:20',
            'category' => 'required|in:announcement,ongoing,upcoming,past',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'date' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:500',
            'details' => 'nullable|json',
            'is_active' => 'nullable|boolean'
        ], [
            'title.required' => 'Event title is required',
            'title.unique' => 'An event with this title already exists',
            'description.required' => 'Event description is required',
            'description.min' => 'Description must be at least 20 characters',
            'category.required' => 'Please select an event category',
            'image.max' => 'Image size must not exceed 5MB'
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

            // Handle image upload with better naming
            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . \Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs('events', $filename, 'public');
            }

            // Get next display order
            $maxOrder = Event::max('display_order') ?? -1;

            // Parse details
            $details = [];
            if ($request->has('details')) {
                $detailsInput = $request->input('details');
                $details = is_string($detailsInput) 
                    ? json_decode($detailsInput, true) ?? [] 
                    : $detailsInput;
            }

            // Create event
            $event = Event::create([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'image_path' => $imagePath,
                'date' => $request->date,
                'location' => $request->location,
                'details' => $details,
                'is_active' => $request->is_active ?? true,
                'display_order' => $maxOrder + 1,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);

            // Log the action
            $event->logAction('created', auth()->id(), null, 'Event created successfully');

            DB::commit();

            \Log::info('✅ Event created', ['id' => $event->id, 'title' => $event->title]);

            return response()->json([
                'success' => true,
                'message' => 'Event "' . $event->title . '" created successfully',
                'event' => $event->load('creator')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('❌ Failed to create event', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create event: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single event with full details
     */
    public function show(Event $event)
    {
        try {
            $event->load(['creator', 'updater', 'logs.performer']);
            
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
                    'display_order' => $event->display_order,
                    'created_at' => $event->created_at->toIso8601String(),
                    'updated_at' => $event->updated_at->toIso8601String(),
                    'creator' => $event->creator ? [
                        'id' => $event->creator->id,
                        'name' => $event->creator->name,
                        'email' => $event->creator->email
                    ] : null,
                    'updater' => $event->updater ? [
                        'id' => $event->updater->id,
                        'name' => $event->updater->name,
                        'email' => $event->updater->email
                    ] : null,
                    'logs' => $event->logs->map(function($log) {
                        return [
                            'id' => $log->id,
                            'action' => $log->action,
                            'performer' => $log->performer_name,
                            'changes' => $log->formatted_changes,
                            'notes' => $log->notes,
                            'created_at' => $log->created_at->toIso8601String(),
                            'created_at_human' => $log->created_at->diffForHumans()
                        ];
                    })
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ Failed to load event', [
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
     * Update an event with change tracking
     */
    public function update(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:events,title,' . $event->id,
            'description' => 'required|string|min:20',
            'category' => 'required|in:announcement,ongoing,upcoming,past',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'date' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:500',
            'details' => 'nullable|json',
            'is_active' => 'nullable|boolean'
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

            // Track changes
            $changes = [];
            $oldData = $event->only(['title', 'description', 'category', 'date', 'location', 'is_active']);

            // Handle image update
            $newImagePath = $event->image_path;
            if ($request->hasFile('image')) {
                // Delete old image
                if ($event->image_path && Storage::disk('public')->exists($event->image_path)) {
                    Storage::disk('public')->delete($event->image_path);
                }
                
                // Store new image
                $file = $request->file('image');
                $filename = time() . '_' . \Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
                $newImagePath = $file->storeAs('events', $filename, 'public');
                
                $changes['image'] = ['old' => $event->image_path, 'new' => $newImagePath];
            }

            // Parse details
            $details = [];
            if ($request->has('details')) {
                $detailsInput = $request->input('details');
                $details = is_string($detailsInput) 
                    ? json_decode($detailsInput, true) ?? [] 
                    : $detailsInput;
            }

            // Update event
            $event->update([
                'title' => $request->title,
                'description' => $request->description,
                'category' => $request->category,
                'image_path' => $newImagePath,
                'date' => $request->date,
                'location' => $request->location,
                'details' => $details,
                'is_active' => $request->is_active ?? $event->is_active,
                'updated_by' => auth()->id()
            ]);

            // Calculate changes
            $newData = $event->only(['title', 'description', 'category', 'date', 'location', 'is_active']);
            foreach ($newData as $key => $value) {
                if ($oldData[$key] !== $value) {
                    $changes[$key] = ['old' => $oldData[$key], 'new' => $value];
                }
            }

            // Log the action
            if (!empty($changes)) {
                $event->logAction('updated', auth()->id(), $changes, 'Event updated');
            }

            DB::commit();

            \Log::info('✅ Event updated', ['id' => $event->id, 'changes' => count($changes)]);

            return response()->json([
                'success' => true,
                'message' => 'Event "' . $event->title . '" updated successfully',
                'event' => $event->fresh()->load('creator', 'updater'),
                'changes_count' => count($changes)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('❌ Failed to update event', [
                'id' => $event->id,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update event: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an event (soft delete)
     */
    public function destroy(Event $event)
    {
        try {
            DB::beginTransaction();

            $eventTitle = $event->title;
            $eventId = $event->id;

            // Log the action before deletion
            $event->logAction('deleted', auth()->id(), null, 'Event deleted by ' . auth()->user()->name);

            // Delete image if exists
            if ($event->image_path && Storage::disk('public')->exists($event->image_path)) {
                Storage::disk('public')->delete($event->image_path);
            }

            // Soft delete the event
            $event->delete();

            DB::commit();

            \Log::info('✅ Event deleted', ['id' => $eventId, 'title' => $eventTitle]);

            return response()->json([
                'success' => true,
                'message' => 'Event "' . $eventTitle . '" deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('❌ Failed to delete event', [
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
     */
    public function toggleStatus(Event $event)
    {
        try {
            $newStatus = !$event->is_active;
            $event->update([
                'is_active' => $newStatus,
                'updated_by' => auth()->id()
            ]);

            $action = $newStatus ? 'published' : 'unpublished';
            $event->logAction($action, auth()->id(), [
                'is_active' => ['old' => !$newStatus, 'new' => $newStatus]
            ], 'Event status changed to ' . ($newStatus ? 'active' : 'inactive'));

            \Log::info('✅ Event status toggled', [
                'id' => $event->id,
                'new_status' => $newStatus ? 'active' : 'inactive'
            ]);

            return response()->json([
                'success' => true,
                'message' => $newStatus 
                    ? 'Event "' . $event->title . '" is now active and visible to public' 
                    : 'Event "' . $event->title . '" is now inactive and hidden from public',
                'is_active' => $newStatus
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ Failed to toggle event status', [
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
     * Update event display order
     */
    public function updateOrder(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'display_order' => 'required|integer|min:0|max:9999'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid display order',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $oldOrder = $event->display_order;
            $newOrder = $request->display_order;

            $event->update([
                'display_order' => $newOrder,
                'updated_by' => auth()->id()
            ]);

            $event->logAction('updated', auth()->id(), [
                'display_order' => ['old' => $oldOrder, 'new' => $newOrder]
            ], 'Display order updated from ' . $oldOrder . ' to ' . $newOrder);

            DB::commit();

            \Log::info('✅ Event display order updated', [
                'id' => $event->id,
                'old' => $oldOrder,
                'new' => $newOrder
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Display order updated successfully',
                'display_order' => $newOrder
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('❌ Failed to update event order', [
                'id' => $event->id,
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update display order'
            ], 500);
        }
    }

    /**
     * Get comprehensive event statistics
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total' => Event::count(),
                'active' => Event::where('is_active', true)->count(),
                'inactive' => Event::where('is_active', false)->count(),
                'announcements' => Event::where('category', 'announcement')->count(),
                'ongoing' => Event::where('category', 'ongoing')->count(),
                'upcoming' => Event::where('category', 'upcoming')->count(),
                'past' => Event::where('category', 'past')->count(),
                'with_images' => Event::whereNotNull('image_path')->count(),
                'without_images' => Event::whereNull('image_path')->count(),
                'recent_events' => Event::orderBy('created_at', 'desc')->take(5)->get(),
                'top_display_order' => Event::orderBy('display_order')->take(5)->get(),
                'recent_logs' => EventLog::with('event', 'performer')
                    ->latest()
                    ->take(10)
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'timestamp' => now()->toIso8601String()
            ]);

        } catch (\Exception $e) {
            \Log::error('❌ Failed to fetch statistics', [
                'message' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics'
            ], 500);
        }
    }

    /**
     * Bulk update event status
     */
    public function bulkToggleStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_ids' => 'required|array',
            'event_ids.*' => 'exists:events,id',
            'is_active' => 'required|boolean'
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

            $count = Event::whereIn('id', $request->event_ids)
                ->update([
                    'is_active' => $request->is_active,
                    'updated_by' => auth()->id()
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $count . ' events updated successfully',
                'count' => $count
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update events'
            ], 500);
        }
    }
}