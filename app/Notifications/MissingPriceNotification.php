<?php

namespace App\Notifications;

use App\Models\Sparepart;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

// Pengumuman Keamanan: Melaporkan ke Superadmin jika ada Admin yang lupa (atau sengaja) menginput barang jualan tanpa mencantumkan harga jualnya.
// (Mencegah peretasan "Bypass Harga" yang lolos dari Form Validasi).
class MissingPriceNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    // Tahap Persiapan: Menerima data barang bermasalah dan nama pelaku (Admin) yang menginputnya.
    public function __construct(
        public Sparepart $sparepart,
        public User $addedBy
    ) {}

    // Saluran Pengiriman: Disimpan ke database (ikon lonceng) dan dikirim Pop-up (Real-Time).
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    // Merakit isi pesan peringatan keamanan tingkat tinggi untuk disimpan permanen di database.
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Peringatan Harga Kosong',
            'message' => __('ui.notification_missing_price', [
                'name' => $this->sparepart->name,
                'admin' => $this->addedBy->name,
            ]),
            'url' => route('inventory.edit', $this->sparepart->uuid).'?focus=price',
            'sparepart_id' => $this->sparepart->id,
            'added_by' => $this->addedBy->name,
            'type' => 'danger',
        ];
    }

    // Merakit peringatan seketika (Toast) yang muncul nyaring di layar Superadmin.
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => __('ui.missing_price_alert'),
            'message' => __('ui.notification_missing_price', [
                'name' => $this->sparepart->name,
                'admin' => $this->addedBy->name,
            ]),
            'url' => route('inventory.edit', $this->sparepart->uuid).'?focus=price',
            'unread_count' => $notifiable->unreadNotifications()->count() + 1,
            'type' => 'danger',
        ]);
    }
}
