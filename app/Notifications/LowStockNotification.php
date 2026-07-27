<?php

namespace App\Notifications;

use App\Models\Sparepart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $sparepart;

    // Tahap Persiapan: Menerima data barang yang stoknya sedang menipis atau sudah habis tak tersisa.
    public function __construct(Sparepart $sparepart)
    {
        $this->sparepart = $sparepart;
    }

    // Saluran Pengiriman: Disimpan ke database (ikon lonceng) dan dikirim Pop-up (Real-Time).
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    // Merakit pesan peringatan darurat (Habis/Menipis) untuk disimpan permanen di database.
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
            'url' => route('inventory.show', $this->sparepart->uuid) . '#stock-history',
            'type' => 'danger',
            'sparepart_id' => $this->sparepart->id,
        ];
    }

    // Merakit isi pesan darurat yang akan muncul tiba-tiba (Pop-up/Toast) di layar Admin.
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        $isDepleted = $this->sparepart->stock <= 0;
        $title = $isDepleted ? 'Stok Habis!' : 'Stok Menipis';

        return new BroadcastMessage([
            'title' => $title,
            'message' => $isDepleted
                ? "Stok {$this->sparepart->name} telah HABIS (0)!"
                : "Stok {$this->sparepart->name} berada di bawah batas minimum ({$this->sparepart->stock} / {$this->sparepart->minimum_stock}).",
            'url' => route('inventory.show', $this->sparepart->uuid) . '#stock-history',
            'type' => 'danger',
        ]);
    }
}
