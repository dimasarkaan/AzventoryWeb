<?php

namespace App\Notifications;

use App\Models\Sparepart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

// Pengumuman Sistem: Peringatan Dini saat stok barang hampir menyentuh batas minimum (masih sisa 100-150% dari minimum).
// Berguna agar Admin bisa membeli/menyiapkan stok tambahan sebelum benar-benar habis.
class ApproachingStockNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public $sparepart;

    public function __construct(Sparepart $sparepart)
    {
        $this->sparepart = $sparepart;
    }

    // Saluran Pengiriman: Disimpan ke database (muncul di ikon lonceng) dan dikirim langsung (Real-Time) ke layar pengguna.
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    // Merakit isi pesan notifikasi untuk disimpan permanen di database.
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Stok Mendekati Batas',
            'message' => "Stok {$this->sparepart->name} tinggal {$this->sparepart->stock} {$this->sparepart->unit}. Segera lakukan pemesanan.",
            'url' => route('inventory.show', $this->sparepart->uuid).'#stock-history',
            'type' => 'warning',
        ];
    }

    // Merakit isi pesan yang akan muncul mendadak (Pop-up/Toast) di layar pengguna.
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Stok Mendekati Batas',
            'message' => "Stok {$this->sparepart->name} tinggal {$this->sparepart->stock} {$this->sparepart->unit}. Segera lakukan pemesanan.",
            'url' => route('inventory.show', $this->sparepart->uuid).'#stock-history',
            'type' => 'warning',
        ]);
    }
}
