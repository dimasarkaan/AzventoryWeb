<?php

namespace App\Notifications;

use App\Models\Sparepart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public $sparepart;

    // Buat instance notifikasi baru.
    public function __construct(Sparepart $sparepart)
    {
        $this->sparepart = $sparepart;
    }

    // Tentukan channel pengiriman notifikasi.
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    // Representasi array dari notifikasi.
    public function toArray(object $notifiable): array
    {
        return [
            'message' => __('ui.notification_low_stock', ['name' => $this->sparepart->name]),
            'url' => route('inventory.show', $this->sparepart->id),
            'sparepart_id' => $this->sparepart->id,
        ];
    }

    // Representasi broadcast dari notifikasi.
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
