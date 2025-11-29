<?php

namespace App\Http\Controllers;

use App\Models\AdminNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get unread count only
     */
    public function getUnreadCount()
    {
        $count = AdminNotification::where('user_id', auth()->id())
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get all notifications with pagination
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $filterRead = $request->get('filter', 'all'); // 'all', 'unread', 'read'
        
        $query = AdminNotification::where('user_id', auth()->id());

        // Apply filter
        if ($filterRead === 'unread') {
            $query->unread();
        } elseif ($filterRead === 'read') {
            $query->read();
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        // Format notifications for display
        $notifications->getCollection()->transform(function ($notif) {
            return [
                'id' => $notif->id,
                'type' => $notif->type,
                'title' => $notif->title,
                'message' => $notif->message,
                'data' => $notif->data,
                'action_url' => $notif->action_url,
                'is_read' => $notif->is_read,
                'icon' => $notif->icon,
                'color' => $notif->color,
                'time_ago' => $notif->time_ago,
                'created_at' => $notif->created_at->format('M d, Y g:i A'),
                'read_at' => $notif->read_at?->format('M d, Y g:i A')
            ];
        });

        if ($request->expectsJson()) {
            return response()->json($notifications);
        }

        return view('admin.notifications.index', compact('notifications', 'filterRead'));
    }

    /**
     * Get dropdown unread notifications (limit 10)
     */
    public function unread()
    {
        $notifications = AdminNotification::where('user_id', auth()->id())
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notif) {
                return [
                    'id' => $notif->id,
                    'type' => $notif->type,
                    'title' => $notif->title,
                    'message' => $notif->message,
                    'data' => $notif->data,
                    'action_url' => $notif->action_url,
                    'is_read' => $notif->is_read,
                    'icon' => $notif->icon,
                    'color' => $notif->color,
                    'time_ago' => $notif->time_ago
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count()
        ]);
    }

    /**
     * Mark single notification as read WITHOUT deleting it
     */
    public function markAsRead($id)
    {
        $notification = AdminNotification::where('user_id', auth()->id())
            ->findOrFail($id);

        // Only mark as read, don't delete
        if (!$notification->is_read) {
            $notification->markAsRead();
        }

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all notifications as read WITHOUT deleting them
     */
    public function markAllAsRead()
    {
        $count = AdminNotification::where('user_id', auth()->id())
            ->unread()
            ->count();

        AdminNotification::where('user_id', auth()->id())
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => "Marked {$count} notification(s) as read"
        ]);
    }

    /**
     * Delete single notification
     */
    public function destroy($id)
    {
        $notification = AdminNotification::where('user_id', auth()->id())
            ->findOrFail($id);

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }

    /**
     * Clear/delete all read notifications
     */
    public function clearRead()
    {
        $count = AdminNotification::where('user_id', auth()->id())
            ->read()
            ->count();

        AdminNotification::where('user_id', auth()->id())
            ->read()
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Cleared {$count} read notification(s)"
        ]);
    }

    /**
     * Delete all notifications
     */
    public function clearAll()
    {
        $count = AdminNotification::where('user_id', auth()->id())
            ->count();

        AdminNotification::where('user_id', auth()->id())
            ->delete();

        return response()->json([
            'success' => true,
            'message' => "Deleted all {$count} notification(s)"
        ]);
    }
}