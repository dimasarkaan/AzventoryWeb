<?php

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

    /**
     * Menampilkan detail peminjaman dan riwayat pengembalian.
     */
    public function show(Borrowing $borrowing)
    {
        $this->authorize('view', $borrowing);

        $borrowing->load(['user', 'sparepart', 'returns' => function ($query) {
            $query->orderBy('return_date', 'desc');
        }]);

        return view('inventory.borrow.show', [
            'borrowing' => $borrowing,
        ]);
    }

    /**
     * Menyimpan data peminjaman baru (Pinjam Barang).
     */
    public function store(StoreBorrowingRequest $request, Sparepart $sparepart)
    {
        $this->authorize('create', Borrowing::class);

        try {
            $this->inventoryService->createBorrowing($sparepart, $request->validated());

            return redirect()->route('inventory.show', $sparepart->id)
                ->with('success', 'Peminjaman berhasil dicatat.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Memperbarui status peminjaman (Kembalikan Barang).
     */
    public function returnItem(ReturnBorrowingRequest $request, Borrowing $borrowing)
    {
        $this->authorize('update', $borrowing);

        $returnPhotos = [];
        if ($request->hasFile('return_photos')) {
            foreach ($request->file('return_photos') as $photo) {
                $returnPhotos[] = $this->imageOptimizer->optimizeAndSave($photo, 'return_evidence');
            }
        }

        try {
            $this->inventoryService->returnBorrowing($borrowing, $request->validated(), $returnPhotos);

            $message = 'Barang berhasil dikembalikan.';

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $message,
                    'status' => 'success',
                ]);
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    public function history(Borrowing $borrowing)
    {
        $this->authorize('view', $borrowing);

        try {
            $borrowing->load('user', 'returns'); // Eager load

            return response()->json([
                'borrower' => $borrowing->user ? $borrowing->user->name : 'User Terhapus',
                'borrow_date' => $borrowing->borrowed_at ? $borrowing->borrowed_at->translatedFormat('d F Y H:i') : '-',
                'total_quantity' => $borrowing->quantity,
                'status' => $borrowing->status,
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
            \Log::error('Error in BorrowingController::history', [
                'borrowing_id' => $borrowing->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat riwayat',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
