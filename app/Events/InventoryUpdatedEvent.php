<?php

namespace App\Events;

use App\Models\Sparepart;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

/**
 * Event untuk broadcast perubahan inventory ke semua user yang sedang online.
 * 
 * Event ini di-trigger saat barang dibuat, diupdate, atau dihapus.
 * Berguna untuk update real-time di dashboard atau inventory list tanpa refresh.
 */
class InventoryUpdatedEvent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $sparepart;
    public $action; // created|updated|deleted
    public $userName;

    public function __construct(Sparepart $sparepart, string $action, string $userName)
    {
        $this->sparepart = $sparepart;
        $this->action = $action;
        $this->userName = $userName;
    }

    // Channel untuk broadcast (public channel).
    public function broadcastOn(): Channel
    {
        return new Channel('inventory-updates');
    }

    // Nama event yang di-broadcast.
    public function broadcastAs(): string
    {
        return 'InventoryUpdated';
    }

    // Data yang dikirim ke frontend.
    public function broadcastWith(): array
    {
        return [
            'id' => $this->sparepart->id,
            'name' => $this->sparepart->name,
            'stock' => $this->sparepart->stock,
            'action' => $this->action,
            'user_name' => $this->userName,
            'message' => $this->generateMessage(),
            'timestamp' => now()->toISOString(),
        ];
    }

    // Generate pesan notifikasi berdasarkan action.
    private function generateMessage(): string
    {
        return match($this->action) {
            'created' => "{$this->userName} menambahkan barang: {$this->sparepart->name}",
            'updated' => "{$this->userName} mengubah data: {$this->sparepart->name}",
            'deleted' => "{$this->userName} menghapus barang: {$this->sparepart->name}",
            default => "Update inventory: {$this->sparepart->name}",
        };
    }
}
