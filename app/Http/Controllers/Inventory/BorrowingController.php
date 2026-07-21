<?php

// Pengatur lalu lintas (Controller) khusus untuk menangani proses peminjaman dan pengembalian barang.
// Mengatur tampilan detail pinjaman, pencatatan peminjaman, hingga proses pengembalian beserta bukti fotonya.
namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\Borrowing\ReturnBorrowingRequest;
use App\Http\Requests\Inventory\Borrowing\StoreBorrowingRequest;
use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Traits\ActivityLogger;

class BorrowingController extends Controller
{
    use ActivityLogger;

    protected $imageOptimizer;

    protected $inventoryService;

    public function __construct(\App\Services\ImageOptimizationService $imageOptimizer, \App\Services\InventoryService $inventoryService)
    {
        $this->imageOptimizer = $imageOptimizer;
        $this->inventoryService = $inventoryService;
    }

    // Menampilkan halaman detail transaksi peminjaman beserta riwayat pengembaliannya
    public function show(Borrowing $borrowing)
    {
        // Memastikan pengguna memiliki izin untuk melihat data peminjaman ini
        // (Misalnya: Operator hanya boleh melihat pinjamannya sendiri)
        $this->authorize('view', $borrowing);

        // Memuat relasi data peminjam, barang yang dipinjam, dan daftar pengembalian (diurutkan dari yang terbaru)
        $borrowing->load(['user', 'sparepart', 'returns' => function ($query) {
            $query->orderBy('return_date', 'desc');
        }]);

        // Tampilkan view detail peminjaman dengan membawa data tersebut
        return view('inventory.borrow.show', [
            'borrowing' => $borrowing,
        ]);
    }

    // Menyimpan data peminjaman barang baru ke dalam sistem
    public function store(StoreBorrowingRequest $request, Sparepart $sparepart)
    {
        // Memeriksa apakah pengguna diizinkan untuk membuat data peminjaman baru
        $this->authorize('create', Borrowing::class);

        try {
            // Melempar data inputan ke InventoryService agar diproses pengurangannya ke stok barang
            $this->inventoryService->createBorrowing($sparepart, $request->validated());

            // Jika peminjaman sukses dicatat, kembali ke halaman detail barang dengan pesan sukses
            return redirect()->route('inventory.show', $sparepart)
                ->with('success', 'Peminjaman berhasil dicatat.');
        } catch (\Exception $e) {
            // Jika gagal (misalnya stok ternyata tidak cukup), tolak dan kembalikan pesan error
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    // Memproses aksi pengembalian barang oleh peminjam (bisa dikembalikan sebagian atau seluruhnya)
    public function returnItem(ReturnBorrowingRequest $request, Borrowing $borrowing)
    {
        // Memastikan pengguna berhak untuk memproses pengembalian data pinjaman ini
        $this->authorize('update', $borrowing);

        $returnPhotos = [];
        // Jika pengguna melampirkan foto bukti pengembalian, kita simpan fotonya
        if ($request->hasFile('return_photos')) {
            foreach ($request->file('return_photos') as $photo) {
                // Foto dioptimasi ukurannya (diperkecil) terlebih dahulu sebelum disimpan agar tidak boros penyimpanan server
                $returnPhotos[] = $this->imageOptimizer->optimizeAndSave($photo, 'return_evidence');
            }
        }

        try {
            // Serahkan proses rumit pengembalian (seperti pengecekan apakah barang rusak/baik dan penambahan stok) ke InventoryService
            $this->inventoryService->returnBorrowing($borrowing, $request->validated(), $returnPhotos);

            $message = 'Barang berhasil dikembalikan.';

            // Jika proses dipanggil via AJAX (JavaScript dari halaman depan)
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $message,
                    'status' => 'success',
                ]);
            }

            // Jika dipanggil secara normal (submit form biasa)
            return back()->with('success', $message);
        } catch (\Exception $e) {
            // Menangkap error (contoh: mencoba mengembalikan jumlah yang lebih banyak dari yang dipinjam)
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    // Endpoint API (AJAX) untuk mengambil data ringkasan dan riwayat pengembalian barang secara spesifik
    public function history(Borrowing $borrowing)
    {
        // Tetap pastikan hak akses keamanan untuk melihat data ini
        $this->authorize('view', $borrowing);

        try {
            // Panggil relasi user dan returns secara bersamaan agar mempercepat query database
            $borrowing->load('user', 'returns'); 

            // Susun data JSON rapi yang siap dikonsumsi oleh frontend
            return response()->json([
                'borrower' => $borrowing->user ? $borrowing->user->name : 'User Terhapus',
                'borrow_date' => $borrowing->borrowed_at ? $borrowing->borrowed_at->translatedFormat('d F Y H:i') : '-',
                'total_quantity' => $borrowing->quantity,
                'status' => $borrowing->status,
                // Mengubah daftar riwayat pengembalian menjadi format array yang gampang dibaca oleh tabel frontend
                'items' => $borrowing->returns->sortByDesc('created_at')->values()->map(function ($return) {
                    return [
                        'id' => $return->id,
                        'date' => $return->return_date ? $return->return_date->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                        'quantity' => $return->quantity,
                        'condition' => $return->condition,
                        'notes' => $return->notes ?? '',
                        'photos' => $return->photos ?? [],
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            // Jika terjadi kesalahan sistem, catat errornya secara diam-diam (silent log) di file log Laravel
            \Log::error('Error in BorrowingController::history', [
                'borrowing_id' => $borrowing->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Lemparkan pesan error ke browser
            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat riwayat',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
