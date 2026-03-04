<?php

namespace App\Notifications;

use App\Models\StockLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class StockRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Inisialisasi notifikasi permintaan perubahan stok (In/Out/Adjustment).
     */
    public function __construct(public StockLog $stockLog, public string $message)
    {
        //
    }

    /**
     * Tentukan channel transmisi notifikasi: Database untuk persistence, Broadcast untuk real-time.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Struktur data notifikasi untuk disimpan di database.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'stock_log_id' => $this->stockLog->id,
            'message' => $this->message,
            'url' => route('inventory.stock-approvals.index'),
        ];
    }

    /**
     * Konten pesan untuk siaran real-time via WebSocket.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Permintaan Stok Baru',
            'message' => $this->message,
            'url' => route('inventory.stock-approvals.index'),
            'unread_count' => $notifiable->unreadNotifications()->count() + 1,
        ]);
    }
}
