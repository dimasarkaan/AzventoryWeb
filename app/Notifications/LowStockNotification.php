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
        $isDepleted = $this->sparepart->stock <= 0;
        $title = $isDepleted ? 'Peringatan: Stok Habis!' : 'Peringatan Stok Menipis';
        $message = $isDepleted
            ? "Stok {$this->sparepart->name} telah HABIS (0). Segera lakukan pengadaan barang."
            : "Stok {$this->sparepart->name} berada di bawah batas minimum ({$this->sparepart->stock} / {$this->sparepart->minimum_stock}).";

        return [
            'title' => $title,
            'message' => $message,
            'url' => route('inventory.show', $this->sparepart->id).'#stock-history',
            'type' => $isDepleted ? 'danger' : 'warning',
            'sparepart_id' => $this->sparepart->id,
        ];
    }

    /**
     * Pesan siaran real-time.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $isDepleted = $this->sparepart->stock <= 0;
        $title = $isDepleted ? 'Stok Habis!' : 'Stok Menipis';

        return new BroadcastMessage([
            'title' => $title,
            'message' => $isDepleted
                ? "Stok {$this->sparepart->name} telah HABIS (0)!"
                : "Stok {$this->sparepart->name} berada di bawah batas minimum ({$this->sparepart->stock} / {$this->sparepart->minimum_stock}).",
            'url' => route('inventory.show', $this->sparepart->id).'#stock-history',
            'type' => $isDepleted ? 'danger' : 'warning',
        ]);
    }
}
