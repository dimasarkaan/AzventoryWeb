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
     * Menampilkan daftar persetujuan stok yang pending (dengan search & filter).
     */
    public function index(Request $request)
    {
        $query = StockLog::with(['sparepart', 'user', 'approver']);

        // Filter Status
        $status = $request->get('status', 'pending');
        if ($status !== 'all' && $status !== '' && $status !== null) {
            $query->where('status', $status);
        }

        // Search: Nama Barang, Part Number, atau Nama Pengaju
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('sparepart', function ($sq) use ($search) {
                    $sq->where('name', 'like', '%'.$search.'%')
                       ->orWhere('part_number', 'like', '%'.$search.'%');
                })->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', '%'.$search.'%')
                       ->orWhere('username', 'like', '%'.$search.'%');
                });
            });
        }

        // Filter Jenis (Masuk/Keluar)
        $filterType = $request->get('filter_type', 'all');
        if ($filterType !== 'all' && $filterType !== '') {
            $query->where('type', $filterType);
        }

        // Best Practice Sorting:
        // 1. Pending First
        // 2. Pending: Urutkan dari yang Terlama (agar tidak ada antrean basi)
        // 3. Selesai (Approved/Rejected): Urutkan dari yang Terbaru
        $pendingApprovals = $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END ASC")
            ->orderByRaw("CASE WHEN status = 'pending' THEN created_at END ASC")
            ->orderByRaw("CASE WHEN status != 'pending' THEN created_at END DESC")
            ->paginate(10)
            ->withQueryString();

        return view('inventory.approvals.index', compact('pendingApprovals'));
    }

    /**
     * Memperbarui status persetujuan stok (Setujui/Tolak).
     */
    public function update(Request $request, StockLog $stock_log)
    {
        $stock_log->load(['sparepart', 'user']);
        $request->validate([
            'status'           => 'required|in:approved,rejected',
            // Gap 1: wajib isi alasan jika menolak
            'rejection_reason' => 'required_if:status,rejected|nullable|max:500',
        ]);

        try {
            $this->inventoryService->approveStockRequest(
                $stock_log,
                $request->status,
                $request->rejection_reason
            );

            // Gap 4: Bedakan flash message approve vs reject
            $message = $request->status === 'approved'
                ? 'Pengajuan berhasil disetujui.'
                : 'Pengajuan berhasil ditolak.';

            return redirect()->route('inventory.stock-approvals.index')
                ->with('success', $message);
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
            'ids'              => 'required|array',
            'ids.*'            => 'exists:stock_logs,id',
            'status'           => 'required|in:approved,rejected',
            // Gap 1: wajib isi alasan jika bulk reject
            'rejection_reason' => 'required_if:status,rejected|nullable|max:500',
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
                $this->inventoryService->approveStockRequest(
                    $log,
                    $request->status,
                    $request->rejection_reason
                );
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "ID {$log->id}: ".$e->getMessage();
            }
        }

        // Gap 4: Bedakan flash message bulk approve vs reject
        $actionText = $request->status === 'approved' ? 'disetujui' : 'ditolak';
        $message = "Berhasil {$actionText} {$successCount} pengajuan.";

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
