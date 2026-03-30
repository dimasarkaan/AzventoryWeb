<?php

namespace App\Notifications;

use App\Models\Sparepart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

/**
 * Notifikasi peringatan stok mendekati minimum (antara 100%—150% dari minimum_stock).
 * Dikirim lebih awal dari LowStockNotification agar admin bisa bertindak sebelum kritis.
 */
class ApproachingStockNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public $sparepart;

    public function __construct(Sparepart $sparepart)
    {
        $this->sparepart = $sparepart;
    }

    /**
     * Channel pengiriman: Database dan Broadcast realtime.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Data yang disimpan di tabel notifications.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Stok Mendekati Batas',
            'message' => "Stok {$this->sparepart->name} tinggal {$this->sparepart->stock} {$this->sparepart->unit}. Segera lakukan pemesanan.",
            'url' => route('inventory.show', $this->sparepart->id).'#stock-history',
            'type' => 'warning',
        ];
    }

    /**
     * Pesan siaran real-time.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Stok Mendekati Batas',
            'message' => "Stok {$this->sparepart->name} tinggal {$this->sparepart->stock} {$this->sparepart->unit}. Segera lakukan pemesanan.",
            'url' => route('inventory.show', $this->sparepart->id).'#stock-history',
            'type' => 'warning',
        ]);
    }
}
