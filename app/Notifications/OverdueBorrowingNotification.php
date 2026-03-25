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

    /**
     * Inisialisasi notifikasi untuk keterlambatan pengembalian barang.
     */
    public function __construct(Borrowing $borrowing)
    {
        $this->borrowing = $borrowing;
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
            'title' => 'Peminjaman Terlambat',
            'message' => "Barang {$this->borrowing->sparepart->name} seharusnya dikembalikan pada {$this->borrowing->expected_return_at->format('d M Y')}.",
            'url' => route('inventory.borrow.show', $this->borrowing->id, false),
            'type' => 'warning',
        ];
    }

    /**
     * Pesan siaran real-time.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Peminjaman Terlambat',
            'message' => "Barang {$this->borrowing->sparepart->name} seharusnya dikembalikan pada {$this->borrowing->expected_return_at->format('d M Y')}.",
            'url' => route('inventory.borrow.show', $this->borrowing->id, false),
            'type' => 'warning',
        ]);
    }
}
