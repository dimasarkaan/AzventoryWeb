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

    /**
     * Create a new notification instance.
     */
    public function __construct(Borrowing $borrowing)
    {
        $this->borrowing = $borrowing;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Peminjaman Terlambat',
            'message' => "Barang {$this->borrowing->sparepart->name} seharusnya dikembalikan pada {$this->borrowing->expected_return_at->format('d M Y')}.",
            'url' => route('borrowings.index'), // Adjusted to general route or specific
            'type' => 'warning',
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Peminjaman Terlambat',
            'message' => "Barang {$this->borrowing->sparepart->name} seharusnya dikembalikan pada {$this->borrowing->expected_return_at->format('d M Y')}.",
            'url' => route('borrowings.index'),
            'type' => 'warning',
        ]);
    }
}
