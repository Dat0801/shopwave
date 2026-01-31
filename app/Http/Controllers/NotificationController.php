<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
            
            // Handle redirection based on notification type
            if (isset($notification->data['slug']) && isset($notification->data['type'])) {
                if ($notification->data['type'] === 'new_post') {
                    return redirect()->route('blog.show', $notification->data['slug']);
                }
            }
        }

        return back();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back();
    }
}
