<?php

namespace App\Notifications;

use App\Models\Borrowing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class OverdueBorrowingNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $borrowing;

    // Tahap Persiapan: Menerima data transaksi peminjaman yang sudah telat/lewat jatuh tempo.
    public function __construct(Borrowing $borrowing)
    {
        $this->borrowing = $borrowing;
    }

    // Saluran Pengiriman: Disimpan ke database (ikon lonceng) dan dikirim Pop-up (Real-Time).
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    // Merakit pesan peringatan telat (Overdue) untuk disimpan permanen di database.
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Peminjaman Terlambat',
            'message' => "Barang {$this->borrowing->sparepart->name} seharusnya dikembalikan pada {$this->borrowing->expected_return_at->format('d M Y')}.",
            'url' => route('inventory.borrow.show', $this->borrowing->id).'?highlight=overdue',
            'type' => 'danger',
        ];
    }

    // Merakit pesan peringatan telat yang akan muncul langsung (Toast) di layar.
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Peminjaman Terlambat',
            'message' => "Barang {$this->borrowing->sparepart->name} seharusnya dikembalikan pada {$this->borrowing->expected_return_at->format('d M Y')}.",
            'url' => route('inventory.borrow.show', $this->borrowing->id).'?highlight=overdue',
            'type' => 'danger',
        ]);
    }
}
