<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    /**
     * Display all events with statistics
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

        // Get statistics
        $stats = [
            'total' => Event::count(),
            'announcements' => Event::where('category', 'announcement')->count(),
            'ongoing' => Event::where('category', 'ongoing')->count(),
            'upcoming' => Event::where('category', 'upcoming')->count(),
            'past' => Event::where('category', 'past')->count(),
            'active' => Event::where('is_active', true)->count(),
        ];

        return view('admin.event.index', compact('events', 'stats'));
    }

    /**
     * Get all events as JSON (for API/AJAX)
     */
    public function getEvents(Request $request)
    {
        try {
            $query = Event::active()->ordered();

            if ($request->has('category') && $request->category !== 'all') {
                $query->where('category', $request->category);
            }

            $events = $query->get()->map(function($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'category' => $event->category,
                    'image' => $event->image_url,
                    'date' => $event->date,
                    'location' => $event->location,
                    'details' => $event->details,
                    'is_active' => $event->is_active,
                    'display_order' => $event->display_order,
                    'created_at' => $event->created_at->toIso8601String(),
                    'updated_at' => $event->updated_at->toIso8601String()
                ];
            });

            return response()->json([
                'success' => true,
                'events' => $events
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch events: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch events'
            ], 500);
        }
    }

    /**
     * Store a new event
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:announcement,ongoing,upcoming,past',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'date' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:500',
            'details' => 'nullable|json',
            'is_active' => 'nullable|boolean'
        ]);

        try {
            DB::beginTransaction();

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imagePath = $file->store('events', 'public');
            }

            // Get next display order
            $maxOrder = Event::max('display_order') ?? -1;

            // Parse details if it's a JSON string
            $details = [];
            if ($request->has('details')) {
                $detailsInput = $request->input('details');
                if (is_string($detailsInput)) {
                    $details = json_decode($detailsInput, true) ?? [];
                } else {
                    $details = $detailsInput;
                }
            }

            // Create event
            $event = Event::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category' => $validated['category'],
                'image_path' => $imagePath,
                'date' => $validated['date'] ?? null,
                'location' => $validated['location'] ?? null,
                'details' => $details,
                'is_active' => $validated['is_active'] ?? true,
                'display_order' => $maxOrder + 1,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);

            // Log the action
            $event->logAction('created', auth()->id(), null, 'Event created');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Event created successfully',
                'event' => $event->load('creator')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create event: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create event: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a single event - FIXED
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
                    'image' => $event->image_url,
                    'date' => $event->date,
                    'location' => $event->location,
                    'details' => $event->details ?? [],
                    'is_active' => (bool) $event->is_active,
                    'display_order' => $event->display_order,
                    'created_at' => $event->created_at->toIso8601String(),
                    'updated_at' => $event->updated_at->toIso8601String(),
                    'creator' => $event->creator ? $event->creator->name : null,
                    'updater' => $event->updater ? $event->updater->name : null,
                    'logs' => $event->logs ?? []
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to load event: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Event not found or failed to load'
            ], 404);
        }
    }

    /**
     * Update an event - FIXED
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:announcement,ongoing,upcoming,past',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'date' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:500',
            'details' => 'nullable|json',
            'is_active' => 'nullable|boolean'
        ]);

        try {
            DB::beginTransaction();

            // Track changes for logging
            $changes = [];
            $oldData = $event->toArray();

            // Handle image update
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($event->image_path && Storage::disk('public')->exists($event->image_path)) {
                    Storage::disk('public')->delete($event->image_path);
                }
                
                // Store new image
                $file = $request->file('image');
                $validated['image_path'] = $file->store('events', 'public');
            }

            // Parse details if it's a JSON string
            $details = [];
            if ($request->has('details')) {
                $detailsInput = $request->input('details');
                if (is_string($detailsInput)) {
                    $details = json_decode($detailsInput, true) ?? [];
                } else {
                    $details = $detailsInput;
                }
            }

            // Update event
            $event->update([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'category' => $validated['category'],
                'image_path' => $validated['image_path'] ?? $event->image_path,
                'date' => $validated['date'] ?? null,
                'location' => $validated['location'] ?? null,
                'details' => $details,
                'is_active' => $validated['is_active'] ?? $event->is_active,
                'updated_by' => auth()->id()
            ]);

            // Calculate changes
            $newData = $event->fresh()->toArray();
            foreach ($newData as $key => $value) {
                if (isset($oldData[$key]) && $oldData[$key] !== $value) {
                    $changes[$key] = [
                        'old' => $oldData[$key],
                        'new' => $value
                    ];
                }
            }

            // Log the action
            $event->logAction('updated', auth()->id(), $changes, 'Event updated');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Event updated successfully',
                'event' => $event->fresh()->load('creator', 'updater')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update event: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update event: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an event
     */
    public function destroy(Event $event)
    {
        try {
            DB::beginTransaction();

            // Log the action before deletion
            $event->logAction('deleted', auth()->id(), null, 'Event deleted');

            // Delete image if exists
            if ($event->image_path && Storage::disk('public')->exists($event->image_path)) {
                Storage::disk('public')->delete($event->image_path);
            }

            // Soft delete the event
            $event->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Event deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete event: ' . $e->getMessage());
            
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
                'is_active' => [
                    'old' => !$newStatus,
                    'new' => $newStatus
                ]
            ], 'Event status changed');

            return response()->json([
                'success' => true,
                'message' => $newStatus ? 'Event activated successfully' : 'Event deactivated successfully',
                'is_active' => $newStatus
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to toggle event status: ' . $e->getMessage());
            
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
        $validated = $request->validate([
            'display_order' => 'required|integer|min:0'
        ]);

        try {
            DB::beginTransaction();

            $oldOrder = $event->display_order;
            $newOrder = $validated['display_order'];

            $event->update([
                'display_order' => $newOrder,
                'updated_by' => auth()->id()
            ]);

            $event->logAction('updated', auth()->id(), [
                'display_order' => [
                    'old' => $oldOrder,
                    'new' => $newOrder
                ]
            ], 'Display order updated');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Event display order updated successfully',
                'display_order' => $newOrder
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update event order: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update display order'
            ], 500);
        }
    }

    /**
     * Get event statistics
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total' => Event::count(),
                'active' => Event::where('is_active', true)->count(),
                'announcements' => Event::where('category', 'announcement')->count(),
                'ongoing' => Event::where('category', 'ongoing')->count(),
                'upcoming' => Event::where('category', 'upcoming')->count(),
                'past' => Event::where('category', 'past')->count(),
                'recent' => Event::orderBy('created_at', 'desc')->take(5)->get()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics'
            ], 500);
        }
    }
}