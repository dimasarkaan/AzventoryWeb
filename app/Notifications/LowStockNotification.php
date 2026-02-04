<?php

namespace App\Notifications;

use App\Models\Sparepart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class LowStockNotification extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public $sparepart;

    /**
     * Create a new notification instance.
     */
    public function __construct(Sparepart $sparepart)
    {
        $this->sparepart = $sparepart;
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
            'message' => 'Stok untuk ' . $this->sparepart->name . ' menipis!',
            'url' => route('superadmin.inventory.show', $this->sparepart->id),
            'sparepart_id' => $this->sparepart->id,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'Stok Menipis!',
            'message' => 'Stok untuk ' . $this->sparepart->name . ' menipis!',
            'url' => route('superadmin.inventory.show', $this->sparepart->id),
            'sparepart_id' => $this->sparepart->id,
        ]);
    }
}
