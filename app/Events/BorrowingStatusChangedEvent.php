<?php

namespace App\Events;

use App\Models\Borrowing;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

/**
 * Event untuk broadcast perubahan status peminjaman.
 * 
 * Di-trigger saat peminjaman di-approve, di-reject, atau barang dikembalikan.
 * Broadcast ke public channel 'inventory-updates'.
 */
class BorrowingStatusChangedEvent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $borrowing;
    public $oldStatus;
    public $newStatus;
    public $adminName;

    public function __construct(
        Borrowing $borrowing,
        string $oldStatus,
        string $newStatus,
        string $adminName
    ) {
        $this->borrowing = $borrowing;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->adminName = $adminName;
    }

    // Channel untuk broadcast (public channel).
    public function broadcastOn(): Channel
    {
        return new Channel('inventory-updates');
    }

    // Nama event yang di-broadcast.
    public function broadcastAs(): string
    {
        return 'BorrowingStatusChanged';
    }

    // Data yang dikirim ke frontend.
    public function broadcastWith(): array
    {
        return [
            'borrowing_id' => $this->borrowing->id,
            'sparepart_name' => $this->borrowing->sparepart->name ?? 'Unknown',
            'borrower_name' => $this->borrowing->user->name ?? 'Unknown',
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'admin_name' => $this->adminName,
            'message' => $this->generateMessage(),
        ];
    }

    // Generate pesan berdasarkan perubahan status.
    private function generateMessage(): string
    {
        $borrower = $this->borrowing->user->name ?? 'User';
        $item = $this->borrowing->sparepart->name ?? 'Barang';
        
        return match($this->newStatus) {
            'approved' => "{$this->adminName} menyetujui peminjaman {$item} oleh {$borrower}",
            'rejected' => "{$this->adminName} menolak peminjaman {$item} oleh {$borrower}",
            'returned' => "{$borrower} mengembalikan {$item}",
            default => "Status peminjaman berubah: {$this->oldStatus} → {$this->newStatus}",
        };
    }
}
