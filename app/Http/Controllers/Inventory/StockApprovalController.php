<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\StockLog;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;

// Controller khusus bagi Admin untuk mengecek dan merespons (Menyetujui/Menolak) permohonan stok dari Operator.
// Mengatur tampilan halaman antrean (Pending), logika penyaringan data (Filter), hingga eksekusi persetujuan massal.
class StockApprovalController extends Controller
{
    use ActivityLogger;

    protected $inventoryService;

    public function __construct(\App\Services\InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    // Menampilkan daftar antrean pengajuan stok yang menunggu keputusan
    // Dilengkapi fitur pencarian (Search) nama barang/user dan penyaringan status (Filter)
    public function index(Request $request)
    {
        $query = StockLog::with(['sparepart', 'user', 'approver']);

        // Filter Status
        $status = $request->input('status', 'pending');
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
        $filterType = $request->input('filter_type', 'all');
        if ($filterType !== 'all' && $filterType !== '') {
            $query->where('type', $filterType);
        }

        // Algoritma Pengurutan (Sorting) Data Terbaik:
        // 1. Munculkan status 'Pending' di posisi paling atas tabel
        // 2. Jika statusnya 'Pending', urutkan dari permohonan Terlama (agar tidak ada antrean menumpuk/basi)
        // 3. Jika statusnya sudah selesai (Di-ACC/Ditolak), urutkan dari riwayat aksi yang Terbaru
        $pendingApprovals = $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END ASC")
            ->orderByRaw("CASE WHEN status = 'pending' THEN created_at END ASC")
            ->orderByRaw("CASE WHEN status != 'pending' THEN created_at END DESC")
            ->paginate(10)
            ->withQueryString();

        return view('inventory.approvals.index', compact('pendingApprovals'));
    }

    // Memproses keputusan Admin (Setuju / Tolak) atas SATU pengajuan stok
    public function update(Request $request, StockLog $stock_log)
    {
        // Pengecekan keamanan: Pastikan hanya orang dengan peran 'Admin' yang boleh merespons form ini
        $this->authorize('update', $stock_log);

        $stock_log->load(['sparepart', 'user']);
        
        // Aturan validasi yang sangat ketat:
        // Jika Admin mengeklik 'Tolak', sistem mewajibkan mereka mengetik 'Alasan Penolakan'
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|max:500',
        ]);

        try {
            // Kita lemparkan logika penambahan stok aslinya dan notifikasinya ke file InventoryService biar Controller tetap ramping
            $this->inventoryService->approveStockRequest(
                $stock_log,
                $request->status,
                $request->rejection_reason
            );

            // Bedakan kata-kata pesan pop-up (Toast) sesuai tombol yang ditekan Admin
            $message = $request->status === 'approved'
                ? 'Pengajuan berhasil disetujui.'
                : 'Pengajuan berhasil ditolak.';

            return redirect()->route('inventory.stock-approvals.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // Mengeksekusi banyak pengajuan stok sekaligus dalam sekali klik (Bulk Action / Centang Kotak)
    public function bulkApprove(Request $request)
    {
        $this->authorize('update', new StockLog);

        // Validasi massal: Pastikan ada ID yang dicentang, dan jika ditolak wajib memberi 1 alasan yang sama untuk semuanya
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:stock_logs,id',
            'status' => 'required|in:approved,rejected',
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

        /** @var \App\Models\StockLog $log */
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

        // Hitung kalimat pesan pop-up (Alert) secara dinamis sesuai status yang dipilih
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
