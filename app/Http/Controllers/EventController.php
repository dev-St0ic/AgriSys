<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\RecycleBinService;
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
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->where('created_at', '<=', $request->date_to . ' 23:59:59');
        }

        $events = $query->orderBy('created_at', 'DESC')->paginate(10);

        $stats = [
            'total'         => Event::notArchived()->count(),
            'active'        => Event::active()->count(),
            'inactive'      => Event::notArchived()->where('is_active', false)->count(),
            'archived'      => Event::archived()->count(),
            'announcements' => Event::active()->where('category', 'announcement')->count(),
            'ongoing'       => Event::active()->where('category', 'ongoing')->count(),
            'upcoming'      => Event::active()->where('category', 'upcoming')->count(),
            'past'          => Event::active()->where('category', 'past')->count(),
        ];

        return view('admin.event.index', compact('events', 'stats'));
    }

    /**
     * Get all ACTIVE events as JSON (PUBLIC API ENDPOINT)
     */
    public function getEvents(Request $request)
    {
        try {
            \Log::info('Events API called', [
                'category' => $request->get('category', 'all'),
                'ip'       => $request->ip(),
            ]);

            $query = Event::active()
                ->orderBy('display_order', 'asc')
                ->orderBy('created_at', 'desc');

            if ($request->has('category') && $request->category !== 'all') {
                $query->where('category', $request->category);
            }

            $events = $query->get();

            \Log::info('Events retrieved', ['count' => $events->count()]);

            $formattedEvents = $events->map(function ($event) {
                return [
                    'id'                => $event->id,
                    'title'             => $event->title,
                    'description'       => $event->description,
                    'short_description' => \Str::limit($event->description, 120),
                    'category'          => $event->category,
                    'category_label'    => ucfirst($event->category),
                    'image'             => $event->image_url,
                    'image_path'        => $event->image_path,
                    'date'              => $event->date ?? 'Date TBA',
                    'location'          => $event->location ?? 'Location TBA',
                    'details'           => $event->details ?? [],
                    'is_active'         => (bool) $event->is_active,
                    'display_order'     => $event->display_order,
                    'formatted_date'    => $event->formatted_date,
                    'created_at'        => $event->created_at->toIso8601String(),
                    'created_at_human'  => $event->created_at->diffForHumans(),
                ];
            });

            return response()->json([
                'success'   => true,
                'events'    => $formattedEvents,
                'count'     => $formattedEvents->count(),
                'timestamp' => now()->toIso8601String()
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Events API Error', ['message' => $e->getMessage()]);

            return response()->json([
                'success'   => false,
                'message'   => 'Failed to load events',
                'error'     => config('app.debug') ? $e->getMessage() : 'An error occurred',
                'timestamp' => now()->toIso8601String()
            ], 500);
        }
    }

    /**
     * Store a new event.
     * No per-category active limit — admins can activate as many events as they want.
     */
    public function store(Request $request)
    {
        \Log::info('Events store request', [
            'category'  => $request->category,
            'has_image' => $request->hasFile('image')
        ]);

        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255|unique:events,title',
            'description' => 'required|string|min:10',
            'category'    => 'required|string|in:announcement,ongoing,upcoming,past',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'date'        => 'nullable|string|max:255',
            'location'    => 'nullable|string|max:500',
            'details'     => 'nullable|string',
            'is_active'   => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $validator->errors()->all()),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Accept whatever status the admin chose — no category restrictions
            $isActive = $request->boolean('is_active', false);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $file      = $request->file('image');
                $filename  = time() . '_' . \Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs('events', $filename, 'public');
            }

            $maxOrder = Event::notArchived()->max('display_order') ?? -1;

            $details = [];
            if ($request->has('details')) {
                $detailsInput = $request->input('details');
                $details = is_string($detailsInput)
                    ? (json_decode($detailsInput, true) ?? [])
                    : $detailsInput;
            }

            $event = Event::create([
                'title'          => $request->title,
                'description'    => $request->description,
                'category'       => $request->category,
                'category_label' => ucfirst($request->category),
                'image_path'     => $imagePath,
                'date'           => $request->date,
                'location'       => $request->location,
                'details'        => $details,
                'is_active'      => $isActive,
                'is_archived'    => false,
                'display_order'  => $maxOrder + 1,
                'created_by'     => auth()->id(),
                'updated_by'     => auth()->id(),
            ]);

            DB::commit();

            \Log::info('Event created', [
                'id'        => $event->id,
                'title'     => $event->title,
                'category'  => $event->category,
                'is_active' => $event->is_active
            ]);

            $message = 'Event "' . $event->title . '" created successfully';
            if (!$event->is_active) {
                $message .= ' (set to inactive — you can activate it later)';
            }

            return response()->json([
                'success'  => true,
                'message'  => $message,
                'event'    => $event->load('creator'),
                'category' => $event->category
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to create event', ['message' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create event',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Get a single event with full details.
     */
    public function show(Event $event)
    {
        try {
            $event->load(['creator', 'updater', 'archivist']);

            return response()->json([
                'success' => true,
                'event'   => [
                    'id'             => $event->id,
                    'title'          => $event->title,
                    'description'    => $event->description,
                    'category'       => $event->category,
                    'category_label' => ucfirst($event->category),
                    'image'          => $event->image_url,
                    'image_path'     => $event->image_path,
                    'date'           => $event->date,
                    'location'       => $event->location,
                    'details'        => $event->details ?? [],
                    'is_active'      => (bool) $event->is_active,
                    'is_archived'    => (bool) $event->is_archived,
                    'archived_at'    => $event->archived_at,
                    'archive_reason' => $event->archive_reason,
                    'display_order'  => $event->display_order,
                    'created_at'     => $event->created_at->toIso8601String(),
                    'updated_at'     => $event->updated_at->toIso8601String(),
                    'creator'   => $event->creator   ? ['id' => $event->creator->id,   'name' => $event->creator->name]   : null,
                    'updater'   => $event->updater   ? ['id' => $event->updater->id,   'name' => $event->updater->name]   : null,
                    'archivist' => $event->archivist ? ['id' => $event->archivist->id, 'name' => $event->archivist->name, 'email' => $event->archivist->email] : null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Event not found'], 404);
        }
    }

    /**
     * Update an event.
     * No per-category active limit — admin can freely set any status.
     */
    public function update(Request $request, Event $event)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255|unique:events,title,' . $event->id,
            'description' => 'required|string|min:10',
            'category'    => 'required|string|in:announcement,ongoing,upcoming,past',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'date'        => 'nullable|string|max:255',
            'location'    => 'nullable|string|max:500',
            'details'     => 'nullable|string',
            'is_active'   => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $isActive = $request->boolean('is_active', $event->is_active);

            $newImagePath = $event->image_path;
            if ($request->hasFile('image')) {
                if ($event->image_path && Storage::disk('public')->exists($event->image_path)) {
                    Storage::disk('public')->delete($event->image_path);
                }
                $file         = $request->file('image');
                $filename     = time() . '_' . \Str::slug($request->title) . '.' . $file->getClientOriginalExtension();
                $newImagePath = $file->storeAs('events', $filename, 'public');
            }

            $details = [];
            if ($request->has('details')) {
                $detailsInput = $request->input('details');
                $details = is_string($detailsInput)
                    ? (json_decode($detailsInput, true) ?? [])
                    : $detailsInput;
            }

            $event->update([
                'title'          => $request->title,
                'description'    => $request->description,
                'category'       => $request->category,
                'category_label' => ucfirst($request->category),
                'image_path'     => $newImagePath,
                'date'           => $request->date,
                'location'       => $request->location,
                'details'        => $details,
                'is_active'      => $isActive,
                'updated_by'     => auth()->id(),
            ]);

            DB::commit();

            \Log::info('Event updated', ['id' => $event->id]);

            return response()->json([
                'success' => true,
                'message' => 'Event "' . $event->title . '" updated successfully',
                'event'   => $event->fresh()->load('creator', 'updater')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to update event', ['id' => $event->id, 'message' => $e->getMessage()]);

            return response()->json(['success' => false, 'message' => 'Failed to update event'], 500);
        }
    }

    /**
     * Archive an event.
     * Safety: cannot archive while still active.
     */
    public function archive(Request $request, Event $event)
    {
        try {
            if ($event->is_active) {
                return response()->json([
                    'success'      => false,
                    'message'      => 'Cannot archive an active event. Please deactivate it first by clicking the Toggle button.',
                    'warning_type' => 'event_is_active'
                ], 422);
            }

            DB::beginTransaction();
            $event->archive(auth()->id(), $request->input('reason'));
            DB::commit();

            \Log::info('Event archived', ['id' => $event->id]);

            return response()->json([
                'success' => true,
                'message' => 'Event "' . $event->title . '" has been archived'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to archive event'], 500);
        }
    }

    /**
     * Restore/unarchive an event.
     */
    public function unarchive(Event $event)
    {
        try {
            DB::beginTransaction();
            $event->unarchive(auth()->id());
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Event "' . $event->title . '" has been restored (remains inactive)'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to restore event'], 500);
        }
    }

    /**
     * Delete an event (soft delete — move to recycle bin).
     * Safety: cannot delete while still active.
     */
    public function destroy(Event $event)
    {
        try {
            if ($event->is_active) {
                return response()->json([
                    'success'      => false,
                    'message'      => 'Cannot delete an active event. Please deactivate it first by clicking the Toggle button.',
                    'warning_type' => 'event_is_active'
                ], 422);
            }

            $eventTitle = $event->title;
            $eventId    = $event->id;

            if (RecycleBinService::softDelete($event, 'Deleted from Events management')) {
                \Log::info('Event moved to recycle bin', ['id' => $eventId, 'title' => $eventTitle]);

                return response()->json([
                    'success' => true,
                    'message' => 'Event "' . $eventTitle . '" has been moved to recycle bin'
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Failed to delete event'], 500);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete event: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Toggle event active status.
     * No per-category limit — freely activate or deactivate any event.
     */
    public function toggleStatus(Event $event)
    {
        try {
            $newStatus = !$event->is_active;

            $event->update([
                'is_active'  => $newStatus,
                'updated_by' => auth()->id(),
            ]);

            \Log::info('Event status toggled', [
                'id'         => $event->id,
                'category'   => $event->category,
                'new_status' => $newStatus ? 'active' : 'inactive'
            ]);

            return response()->json([
                'success'   => true,
                'message'   => $newStatus
                    ? 'Event "' . $event->title . '" is now active'
                    : 'Event "' . $event->title . '" is now inactive',
                'is_active' => $newStatus,
                'category'  => $event->category
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update status'], 500);
        }
    }

    /**
     * Get archived events for archive management view.
     */
    public function archivedEvents(Request $request)
    {
        try {
            $query = Event::archived()
                ->with(['creator', 'updater', 'archivist'])
                ->orderBy('archived_at', 'desc');

            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $events = $query->paginate(20);

            return view('admin.event.archived', compact('events'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to fetch archived events');
        }
    }

    /**
     * Get comprehensive event statistics.
     */
    public function getStatistics()
    {
        try {
            $stats = [
                'total'         => Event::notArchived()->count(),
                'active'        => Event::active()->count(),
                'inactive'      => Event::notArchived()->where('is_active', false)->count(),
                'archived'      => Event::archived()->count(),
                'announcements' => Event::active()->where('category', 'announcement')->count(),
                'ongoing'       => Event::active()->where('category', 'ongoing')->count(),
                'upcoming'      => Event::active()->where('category', 'upcoming')->count(),
                'past'          => Event::active()->where('category', 'past')->count(),
            ];

            return response()->json([
                'success'   => true,
                'stats'     => $stats,
                'timestamp' => now()->toIso8601String()
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to fetch statistics'], 500);
        }
    }

    /**
     * Bulk activate events.
     */
    public function bulkActivate(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No events selected'], 422);
        }

        $updated = Event::notArchived()->whereIn('id', $ids)->update([
            'is_active'  => true,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$updated} event(s) activated successfully",
        ]);
    }

    /**
     * Bulk deactivate events.
     */
    public function bulkDeactivate(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No events selected'], 422);
        }

        $updated = Event::notArchived()->whereIn('id', $ids)->update([
            'is_active'  => false,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$updated} event(s) deactivated successfully",
        ]);
    }

    /**
     * Bulk archive events (only inactive ones).
     */
    public function bulkArchive(Request $request)
    {
        $ids    = $request->input('ids', []);
        $reason = $request->input('reason');

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No events selected'], 422);
        }

        $events  = Event::notArchived()->whereIn('id', $ids)->get();
        $archived = 0;
        $skipped  = 0;

        foreach ($events as $event) {
            if ($event->is_active) {
                $skipped++;
                continue; // skip active events
            }
            $event->archive(auth()->id(), $reason);
            $archived++;
        }

        $message = "{$archived} event(s) archived successfully";
        if ($skipped > 0) {
            $message .= ". {$skipped} active event(s) were skipped (deactivate them first).";
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    /**
     * Bulk restore archived events.
     */
    public function bulkRestore(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No events selected'], 422);
        }

        $events = Event::archived()->whereIn('id', $ids)->get();
        $restored = 0;

        foreach ($events as $event) {
            $event->unarchive(auth()->id());
            $restored++;
        }

        return response()->json([
            'success' => true,
            'message' => "{$restored} event(s) restored successfully",
        ]);
    }

    /**
     * Bulk delete events (move to recycle bin).
     * Works from both the main index and archived page.
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No events selected'], 422);
        }

        // Use whereIn without scope restriction so it works from both pages
        $events  = Event::whereIn('id', $ids)->get();
        $deleted = 0;
        $skipped = 0;

        foreach ($events as $event) {
            if ($event->is_active) {
                $skipped++;
                continue;
            }
            if (RecycleBinService::softDelete($event, 'Bulk deleted from Events management')) {
                $deleted++;
            }
        }

        $message = "{$deleted} event(s) moved to recycle bin";
        if ($skipped > 0) {
            $message .= ". {$skipped} active event(s) were skipped (deactivate them first).";
        }

        return response()->json(['success' => true, 'message' => $message]);
    }
}