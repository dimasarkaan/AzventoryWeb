<?php

namespace App\Notifications;

use App\Enums\UserRole;
use App\Models\StockLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class StockRequestNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    // Tahap Persiapan: Menerima data pengajuan pergerakan stok dan pesan balasannya (Setuju/Tolak).
    public function __construct(public StockLog $stockLog, public string $message)
    {
        //
    }

    // Saluran Pengiriman: Disimpan ke database (ikon lonceng) dan dikirim Pop-up (Real-Time).
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    // Merakit informasi lengkap pengajuan (termasuk alasan penolakan jika ada) untuk disimpan permanen.
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

    // Merakit pesan pengumuman hasil persetujuan yang akan muncul mendadak (Pop-up/Toast) di layar.
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
