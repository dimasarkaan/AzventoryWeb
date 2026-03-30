<?php

namespace App\Notifications;

use App\Enums\UserRole;
use App\Models\StockLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class StockRequestNotification extends Notification implements ShouldBroadcast, ShouldQueue
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
        $url = route('inventory.stock-approvals.index', ['search' => $this->stockLog->sparepart->name]);

        if ($notifiable->role === UserRole::OPERATOR) {
            $url = route('inventory.show', $this->stockLog->sparepart_id);
        }

        return [
            'stock_log_id' => $this->stockLog->id,
            'message' => $this->message,
            'url' => $url,
            'rejection_reason' => $this->stockLog->rejection_reason,
        ];
    }

    /**
     * Konten pesan untuk siaran real-time via WebSocket.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $url = route('inventory.stock-approvals.index', ['search' => $this->stockLog->sparepart->name]);
        if ($notifiable->role === UserRole::OPERATOR) {
            $url = route('inventory.show', $this->stockLog->sparepart_id);
        }

        return new BroadcastMessage([
            'title' => 'Update Status Pengajuan Stok',
            'message' => $this->message,
            'url' => $url,
            'unread_count' => $notifiable->unreadNotifications()->count() + 1,
        ]);
    }
}
