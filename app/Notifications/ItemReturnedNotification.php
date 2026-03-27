<?php

namespace App\Notifications;

use App\Models\Borrowing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

/**
 * Notifikasi yang dikirim ke Admin/Superadmin saat barang pinjaman dikembalikan.
 * Membantu Admin mengetahui status ketersediaan barang secara real-time.
 */
class ItemReturnedNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    /**
     * Inisialisasi notifikasi pengembalian barang.
     */
    public function __construct(
        public Borrowing $borrowing,
        public int $quantity,
        public string $condition
    ) {}

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
            'title' => 'Barang Dikembalikan',
            'message' => "{$this->borrowing->borrower_name} mengembalikan {$this->quantity} {$this->borrowing->sparepart->unit} '{$this->borrowing->sparepart->name}' dalam kondisi {$this->condition}.",
            'url' => route('inventory.borrow.show', $this->borrowing->id),
            'type' => 'success',
        ];
    }

    /**
     * Pesan siaran real-time.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Barang Dikembalikan',
            'message' => "{$this->borrowing->borrower_name} mengembalikan {$this->quantity} {$this->borrowing->sparepart->unit} '{$this->borrowing->sparepart->name}' dalam kondisi {$this->condition}.",
            'url' => route('inventory.borrow.show', $this->borrowing->id),
            'type' => 'success',
        ]);
    }
}
