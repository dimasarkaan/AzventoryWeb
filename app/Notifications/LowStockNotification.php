<?php

namespace App\Notifications;

use App\Models\Sparepart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public $sparepart;

    /**
     * Inisialisasi notifikasi peringatan stok menipis.
     */
    public function __construct(Sparepart $sparepart)
    {
        $this->sparepart = $sparepart;
    }

    /**
     * Channel pengiriman: Database dan Real-time Broadcast.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Data persistensi notifikasi dalam database.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => __('ui.notification_low_stock', ['name' => $this->sparepart->name]),
            'url' => route('inventory.show', $this->sparepart->id),
            'sparepart_id' => $this->sparepart->id,
        ];
    }

    /**
     * Pesan siaran real-time.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => __('ui.low_stock_alert'),
            'message' => __('ui.notification_low_stock', ['name' => $this->sparepart->name]),
            'url' => route('inventory.show', $this->sparepart->id),
            'sparepart_id' => $this->sparepart->id,
        ]);
    }
}
