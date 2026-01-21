<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Borrowing;

class OverdueBorrowingNotification extends Notification
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
        return ['database'];
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
            'link' => route('superadmin.inventory.show', $this->borrowing->sparepart_id), // Or relevant user link
            'type' => 'warning',
        ];
    }
}
