<?php

namespace App\Notifications;

use App\Models\Borrowing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

// Pengumuman Sistem: Memberitahu Admin bahwa ada karyawan yang baru saja memulangkan barang pinjamannya.
// Membantu Admin mengetahui update status ketersediaan barang secara real-time.
class ItemReturnedNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    // Tahap Persiapan: Menerima data transaksi peminjaman, jumlah yang dikembalikan, dan kondisinya (Baik/Rusak/Hilang).
    public function __construct(
        public Borrowing $borrowing,
        public int $quantity,
        public string $condition
    ) {}

    // Saluran Pengiriman: Disimpan ke database (ikon lonceng) dan dikirim Pop-up (Real-Time).
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    // Merakit isi pesan notifikasi untuk disimpan permanen di database.
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Barang Dikembalikan',
            'message' => "{$this->borrowing->borrower_name} mengembalikan {$this->quantity} {$this->borrowing->sparepart->unit} '{$this->borrowing->sparepart->name}' dalam kondisi {$this->condition}.",
            'url' => route('inventory.borrow.show', $this->borrowing->id),
            'type' => match(strtolower($this->condition)) {
                'rusak' => 'warning',
                'hilang' => 'danger',
                default => 'success',
            },
        ];
    }

    // Merakit isi pesan yang akan melayang (Toast) secara seketika di layar Admin.
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Barang Dikembalikan',
            'message' => "{$this->borrowing->borrower_name} mengembalikan {$this->quantity} {$this->borrowing->sparepart->unit} '{$this->borrowing->sparepart->name}' dalam kondisi {$this->condition}.",
            'url' => route('inventory.borrow.show', $this->borrowing->id),
            'type' => match(strtolower($this->condition)) {
                'rusak' => 'warning',
                'hilang' => 'danger',
                default => 'success',
            },
        ]);
    }
}
