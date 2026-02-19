<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Menampilkan daftar notifikasi user.
     */
    public function index(Request $request)
    {
        if ($request->wantsJson()) {
            // Hanya kembalikan notifikasi belum dibaca jika request JSON
            return $request->user()->unreadNotifications()->latest()->take(5)->get();
        }

        $notifications = $request->user()->notifications()->paginate(10);
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Menandai notifikasi spesifik sebagai sudah dibaca.
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        // Redirect kontekstual berdasarkan tipe notifikasi/data
        if (isset($notification->data['url'])) {
            return $request->wantsJson() 
                ? response()->json(['url' => $notification->data['url']])
                : redirect($notification->data['url']);
        }
        
        // Redirect fallback
        if ($notification->type === 'App\Notifications\LowStockNotification') {
             $url = route('inventory.index');
             return $request->wantsJson() 
                ? response()->json(['url' => $url])
                : redirect($url);
        }

        return $request->wantsJson() 
            ? response()->json(['success' => true]) 
            : back();
    }

    /**
     * Menandai semua notifikasi sebagai sudah dibaca.
     */
    public function markAllAsRead(Request $request)
    {
        // Gunakan query builder untuk update langsung (lebih cepat & andal)
        $request->user()->unreadNotifications()->update(['read_at' => now()]);
        
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Semua notifikasi ditandai dibaca.']);
        }

        return back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }
}
