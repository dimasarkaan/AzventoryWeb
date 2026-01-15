<?php

namespace App\Http\Controllers\SuperAdmin;

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
    public function index()
    {
        $pendingApprovals = StockLog::with(['sparepart', 'user'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('superadmin.approvals.index', compact('pendingApprovals'));
    }

    public function update(Request $request, StockLog $stock_log)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        if ($stock_log->status !== 'pending') {
            return redirect()->route('superadmin.stock-approvals.index')
                ->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($request, $stock_log) {
            if ($request->status === 'approved') {
                $sparepart = $stock_log->sparepart;

                if ($stock_log->type === 'masuk') {
                    $sparepart->stock += $stock_log->quantity;
                } else { // keluar
                    if ($sparepart->stock < $stock_log->quantity) {
                        // Batalkan transaksi jika stok tidak mencukupi
                        throw new \Exception('Stok tidak mencukupi untuk transaksi ini.');
                    }
                    $sparepart->stock -= $stock_log->quantity;
                }
                $sparepart->save();

                // Check for low stock after update
                if ($sparepart->minimum_stock > 0 && $sparepart->stock <= $sparepart->minimum_stock) {
                    $admins = User::whereIn('role', ['superadmin', 'admin'])->get();
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
        $message = "Pengajuan stok {$stock_log->type} Anda untuk {$stock_log->sparepart->name} telah {$statusText}.";
        Notification::send($requester, new StockRequestNotification($stock_log, $message));

        return redirect()->route('superadmin.stock-approvals.index')
            ->with('success', 'Status pengajuan berhasil diperbarui.');
    }
}
