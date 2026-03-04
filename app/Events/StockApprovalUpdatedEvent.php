<?php

namespace App\Events;

use App\Models\StockLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event untuk broadcast update status persetujuan stok (baru, disetujui, ditolak)
 * ke Admin / Superadmin secara real-time.
 */
class StockApprovalUpdatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stockLog;
    public $action; // 'created', 'processed' (approved/rejected)

    /**
     * Create a new event instance.
     */
    public function __construct(StockLog $stockLog, string $action)
    {
        // Load relasi yang dibutuhkan untuk UI
        $this->stockLog = $stockLog->loadMissing(['sparepart', 'user']);
        $this->action = $action;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('stock-approvals'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'StockApprovalUpdated';
    }

    public function broadcastWith(): array
    {
        $sparepart = $this->stockLog->sparepart;
        $user = $this->stockLog->user;

        return [
            'id' => $this->stockLog->id,
            'action' => $this->action,
            'log' => [
                'id' => $this->stockLog->id,
                'type' => $this->stockLog->type, // 'masuk' / 'keluar'
                'quantity' => $this->stockLog->quantity,
                'reason' => $this->stockLog->reason,
                'status' => $this->stockLog->status,
                'created_at' => $this->stockLog->created_at->format('d/m/y H:i'),
                'raw_created_at' => $this->stockLog->created_at->toIso8601String(),
                'sparepart' => [
                    'id' => $sparepart ? $sparepart->id : null,
                    'name' => $sparepart ? $sparepart->name : 'N/A',
                    'unit' => $sparepart ? $sparepart->unit : 'Pcs',
                    'image_url' => ($sparepart && $sparepart->image) ? asset('storage/'.$sparepart->image) : null,
                ],
                'user' => [
                    'name' => $user ? $user->name : 'System',
                ]
            ]
        ];
    }
}
