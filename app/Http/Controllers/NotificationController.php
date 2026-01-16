<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            return $request->user()->unreadNotifications()->latest()->take(5)->get();
        }

        $notifications = $request->user()->notifications()->paginate(10);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // Context-aware redirect based on notification type/data
        if (isset($notification->data['url'])) {
            return $request->wantsJson() 
                ? response()->json(['url' => $notification->data['url']])
                : redirect($notification->data['url']);
        }
        
        // Fallback redirects
        if ($notification->type === 'App\Notifications\LowStockNotification') {
             $url = route('superadmin.inventory.index');
             return $request->wantsJson() 
                ? response()->json(['url' => $url])
                : redirect($url);
        }

        return $request->wantsJson() 
            ? response()->json(['success' => true]) 
            : back();
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
