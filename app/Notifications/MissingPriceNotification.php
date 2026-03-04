<?php

namespace App\Notifications;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

/**
 * Notifikasi yang dikirim ke Superadmin ketika Admin menginput
 * barang bertipe 'sale' tanpa mengisi harga (harga = 0).
 */
class MissingPriceNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    /**
     * Inisialisasi notifikasi peringatan harga kosong untuk barang bertipe 'sale'.
     */
    public function __construct(
        public Sparepart $sparepart,
        public User $addedBy
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
            'message' => __('ui.notification_missing_price', [
                'name' => $this->sparepart->name,
                'admin' => $this->addedBy->name,
            ]),
            'url' => route('inventory.edit', $this->sparepart->id),
            'sparepart_id' => $this->sparepart->id,
            'added_by' => $this->addedBy->name,
        ];
    }

    /**
     * Pesan siaran real-time.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => __('ui.missing_price_alert'),
            'message' => __('ui.notification_missing_price', [
                'name' => $this->sparepart->name,
                'admin' => $this->addedBy->name,
            ]),
            'url' => route('inventory.edit', $this->sparepart->id),
            'unread_count' => $notifiable->unreadNotifications()->count() + 1,
        ]);
    }
}
