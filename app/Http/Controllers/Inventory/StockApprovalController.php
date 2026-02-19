<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockLog;
use App\Models\User;
use App\Notifications\LowStockNotification;
use App\Notifications\StockRequestNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Traits\ActivityLogger;

class StockApprovalController extends Controller
{
    use ActivityLogger;

    protected $inventoryService;

    public function __construct(\App\Services\InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Menampilkan daftar persetujuan stok yang pending.
     */
    public function index()
    {
        $pendingApprovals = StockLog::with(['sparepart', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('inventory.approvals.index', compact('pendingApprovals'));
    }

    /**
     * Memperbarui status persetujuan stok (Setujui/Tolak).
     */
    public function update(Request $request, StockLog $stock_log)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        if ($stock_log->status !== 'pending') {
            return redirect()->route('inventory.stock-approvals.index')
                ->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        if ($request->status === 'approved' && $stock_log->type === 'keluar') {
            // Pre-check stock availability
            if ($stock_log->sparepart->stock < $stock_log->quantity) {
                return redirect()->back()->with('error', 'Stok tidak mencukupi untuk menyetujui permintaan ini. Sisa stok: ' . $stock_log->sparepart->stock);
            }
        }

        DB::transaction(function () use ($request, $stock_log) {
            if ($request->status === 'approved') {
                $sparepart = $stock_log->sparepart;

                if ($stock_log->type === 'masuk') {
                    $sparepart->stock += $stock_log->quantity;
                } else { // keluar
                    // Double check inside transaction to be safe from race conditions
                    if ($sparepart->stock < $stock_log->quantity) {
                         throw new \Exception('Stok berubah saat pemrosesan. Transaksi dibatalkan.');
                    }
                    $sparepart->stock -= $stock_log->quantity;
                }
                $sparepart->save();

                // Clear Dashboard Cache & Broadcast Update
                $this->inventoryService->clearCache();
                $this->inventoryService->broadcastUpdate($sparepart, 'updated');

                // Check for low stock after update
                if ($sparepart->minimum_stock > 0 && $sparepart->stock <= $sparepart->minimum_stock) {
                    $admins = User::whereIn('role', [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])->get();
                    Notification::send($admins, new LowStockNotification($sparepart));
                }
            }

            $stock_log->update([
                'status' => $request->status,
                'approved_by' => Auth::id(),
            ]);
        });

        $statusText = $request->status === 'approved' ? 'disetujui' : 'ditolak';
        $this->logActivity(
            'Persetujuan Stok',
            "Pengajuan stok {$stock_log->type} untuk '{$stock_log->sparepart->name}' sejumlah {$stock_log->quantity} telah {$statusText}."
        );

        // Notify the user who made the request
        $requester = $stock_log->user;
        $message = __('ui.notification_stock_request_body', [
            'type' => $stock_log->type,
            'name' => $stock_log->sparepart->name,
            'status' => $statusText
        ]);
        Notification::send($requester, new StockRequestNotification($stock_log, $message));

        return redirect()->route('inventory.stock-approvals.index')
            ->with('success', 'Status pengajuan berhasil diperbarui.');
    }
}
