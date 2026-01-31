<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the current user.
     */
    public function index(): JsonResponse
    {
        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return response()->json([
            'notifications' => $notifications->items(),
            'unread_count' => Auth::user()->unreadNotifications()->count(),
            'total' => $notifications->total(),
        ]);
    }

    /**
     * Get unread notifications for the current user.
     */
    public function unread(): JsonResponse
    {
        $notifications = Auth::user()
            ->unreadNotifications()
            ->latest()
            ->get();

        return response()->json([
            'notifications' => $notifications,
            'count' => $notifications->count(),
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            
            // Handle redirection based on notification type
            if (isset($notification->data['type'])) {
                $type = $notification->data['type'];
                
                if ($type === 'new_order' && isset($notification->data['order_id'])) {
                    return redirect()->route('admin.orders.show', $notification->data['order_id']);
                } elseif ($type === 'new_contact' && isset($notification->data['contact_id'])) {
                    return redirect()->route('admin.contacts.show', $notification->data['contact_id']);
                } elseif ($type === 'low_stock' && isset($notification->data['product_id'])) {
                    return redirect()->route('admin.products.show', $notification->data['product_id']);
                } elseif ($type === 'new_post' && isset($notification->data['slug'])) {
                    return redirect()->route('blog.show', $notification->data['slug']);
                }
            }
        }

        return back();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    /**
     * Delete a notification.
     */
    public function delete($id): JsonResponse
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }
}
