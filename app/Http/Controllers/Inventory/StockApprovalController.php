<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockLog;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;

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
        $stock_log->load(['sparepart', 'user']);
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        try {
            $this->inventoryService->approveStockRequest($stock_log, $request->status);

            return redirect()->route('inventory.stock-approvals.index')
                ->with('success', 'Status pengajuan berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Setujui banyak pengajuan sekaligus.
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:stock_logs,id',
            'status' => 'required|in:approved,rejected',
        ]);

        $logs = StockLog::with(['sparepart', 'user'])->whereIn('id', $request->ids)
            ->where('status', 'pending')
            ->get();

        if ($logs->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada pengajuan pending yang valid untuk diproses.');
        }

        $successCount = 0;
        $errors = [];

        foreach ($logs as $log) {
            try {
                $this->inventoryService->approveStockRequest($log, $request->status);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "ID {$log->id}: ".$e->getMessage();
            }
        }

        $message = "Berhasil memproses {$successCount} pengajuan.";
        if (! empty($errors)) {
            $message .= ' Gagal pada '.count($errors).' pengajuan.';

            return redirect()->route('inventory.stock-approvals.index')
                ->with('warning', $message)
                ->with('errors_list', $errors);
        }

        return redirect()->route('inventory.stock-approvals.index')
            ->with('success', $message);
    }
}
