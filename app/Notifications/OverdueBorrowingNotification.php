<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;

class OverdueBorrowingNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $borrowing;

    // Buat instance notifikasi baru.
    public function __construct(Borrowing $borrowing)
    {
        $this->borrowing = $borrowing;
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
            'title' => 'Peminjaman Terlambat',
            'message' => "Barang {$this->borrowing->sparepart->name} seharusnya dikembalikan pada {$this->borrowing->expected_return_at->format('d M Y')}.",
            'url' => route('inventory.borrow.show', $this->borrowing->id), // Follows general route
            'type' => 'warning',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Peminjaman Terlambat',
            'message' => "Barang {$this->borrowing->sparepart->name} seharusnya dikembalikan pada {$this->borrowing->expected_return_at->format('d M Y')}.",
            'url' => route('inventory.borrow.show', $this->borrowing->id),
            'type' => 'warning',
        ]);
    }
}
