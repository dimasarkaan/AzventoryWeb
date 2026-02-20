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
     * Buat instance notifikasi baru.
     */
    public function __construct(
        public Sparepart $sparepart,
        public User $addedBy
    ) {}

    /**
     * Tentukan channel pengiriman notifikasi.
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Representasi array dari notifikasi (disimpan ke tabel notifications).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message'      => __('ui.notification_missing_price', [
                'name'  => $this->sparepart->name,
                'admin' => $this->addedBy->name,
            ]),
            'url'          => route('inventory.edit', $this->sparepart->id),
            'sparepart_id' => $this->sparepart->id,
            'added_by'     => $this->addedBy->name,
        ];
    }

    /**
     * Representasi broadcast dari notifikasi (dikirim via Reverb WebSocket).
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title'       => __('ui.missing_price_alert'),
            'message'     => __('ui.notification_missing_price', [
                'name'  => $this->sparepart->name,
                'admin' => $this->addedBy->name,
            ]),
            'url'         => route('inventory.edit', $this->sparepart->id),
            'unread_count' => $notifiable->unreadNotifications()->count() + 1,
        ]);
    }
}
