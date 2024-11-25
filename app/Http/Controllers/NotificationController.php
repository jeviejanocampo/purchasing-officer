<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification; // Make sure you import your Notification model

class NotificationController extends Controller
{
    /**
     * Get all low stock notifications
     */
    public function getLowStockNotifications()
    {
        // Fetch notifications related to low stock (or any condition you define)
        $notifications = Notification::where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'message' => $notification->message,
                    'time' => $notification->created_at->diffForHumans(),
                    'is_read' => $notification->is_read,
                ];
            });

        return response()->json($notifications);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        $notification->is_read = true;
        $notification->save();

        return response()->json(['success' => true]);
    }

    /**
     * Mark a notification as unread
     */
    public function markAsUnread($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        $notification->is_read = false;
        $notification->save();

        return response()->json(['success' => true]);
    }
}
