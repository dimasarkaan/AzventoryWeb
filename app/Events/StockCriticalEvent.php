<?php

namespace App\Events;

use App\Models\Sparepart;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

/**
 * Event untuk alert stok kritis.
 * 
 * Di-trigger saat stock < 50% dari minimum stock atau habis total.
 * Broadcast ke public channel 'stock-alerts' untuk semua Admin/Superadmin.
 */
class StockCriticalEvent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $sparepart;
    public $severity; // 'critical' | 'warning' | 'depleted'

    public function __construct(Sparepart $sparepart, string $severity = 'critical')
    {
        $this->sparepart = $sparepart;
        $this->severity = $severity;
    }

    // Channel untuk broadcast (public channel).
    public function broadcastOn(): Channel
    {
        return new Channel('stock-alerts');
    }

    // Nama event yang di-broadcast.
    public function broadcastAs(): string
    {
        return 'StockCritical';
    }

    // Data yang dikirim ke frontend.
    public function broadcastWith(): array
    {
        return [
            'id' => $this->sparepart->id,
            'name' => $this->sparepart->name,
            'part_number' => $this->sparepart->part_number,
            'current_stock' => $this->sparepart->stock,
            'min_stock' => $this->sparepart->minimum_stock,
            'severity' => $this->severity,
            'percentage' => $this->calculatePercentage(),
            'url' => route('inventory.show', $this->sparepart->id),
        ];
    }

    // Hitung persentase stock vs minimum stock.
    private function calculatePercentage(): float
    {
        if ($this->sparepart->minimum_stock == 0) {
            return 0;
        }
        
        return round(($this->sparepart->stock / $this->sparepart->minimum_stock) * 100, 2);
    }
}
