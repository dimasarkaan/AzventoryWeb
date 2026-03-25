<?php

namespace App\Notifications;

use App\Models\Sparepart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notifikasi peringatan stok mendekati minimum (antara 100%—150% dari minimum_stock).
 * Dikirim lebih awal dari LowStockNotification agar admin bisa bertindak sebelum kritis.
 */
class ApproachingStockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $sparepart;

    public function __construct(Sparepart $sparepart)
    {
        $this->sparepart = $sparepart;
    }

    /**
     * Channel pengiriman: hanya Database (tidak perlu broadcast realtime seperti LowStock).
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Data yang disimpan di tabel notifications.
     */
    public function toArray(object $notifiable): array
    {
        $percentage = $this->sparepart->minimum_stock > 0
            ? round(($this->sparepart->stock / $this->sparepart->minimum_stock) * 100)
            : 0;

        return [
            'type'         => 'approaching_stock',
            'message'      => "Stok '{$this->sparepart->name}' mendekati batas minimum ({$this->sparepart->stock}/{$this->sparepart->minimum_stock} unit, {$percentage}%).",
            'url'          => route('inventory.show', $this->sparepart->id, false),
            'sparepart_id' => $this->sparepart->id,
            'current_stock'=> $this->sparepart->stock,
            'minimum_stock'=> $this->sparepart->minimum_stock,
            'percentage'   => $percentage,
        ];
    }
}
