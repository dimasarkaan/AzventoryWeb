<?php

namespace App\Http\Controllers\Inventory\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Services\ImageOptimizationService;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * @group Peminjaman Barang
 *
 * Bagian ini digunakan untuk mencatat peminjaman barang inventaris.
 *
 * API ini melacak siapa yang meminjam, kapan barang harus dikembalikan, dan bagaimana kondisi barang saat dikembalikan.
 */
class BorrowingController extends Controller
{
    protected $inventoryService;

    protected $imageOptimizer;

    public function __construct(InventoryService $inventoryService, ImageOptimizationService $imageOptimizer)
    {
        $this->inventoryService = $inventoryService;
        $this->imageOptimizer = $imageOptimizer;
    }

    /**
     * Mendapatkan daftar peminjaman aktif/riwayat.
     */
    public function index(Request $request)
    {
        $query = Borrowing::with(['user', 'sparepart']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $borrowings = $query->latest()->paginate($request->input('per_page', 20))->withQueryString();

        return response()->json($borrowings);
    }

    /**
     * Mencatat peminjaman baru via API.
     *
     * @urlParam sparepart string required UUID dari barang inventaris. Example: 9a5f3b...
     */
    public function store(\App\Http\Requests\Inventory\Borrowing\StoreBorrowingRequest $request, Sparepart $sparepart)
    {
        // Cek otorisasi
        if (Gate::denies('create', Borrowing::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $borrowing = $this->inventoryService->createBorrowing($sparepart, $request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Peminjaman berhasil dicatat via API',
                'data' => $borrowing->load(['sparepart', 'user']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Detail peminjaman.
     *
     * @urlParam borrowing string required UUID dari data peminjaman. Example: 7b3e1c...
     */
    public function show($id)
    {
        $borrowing = Borrowing::with(['user', 'sparepart', 'returns'])->where('uuid', $id)->first();

        if (! $borrowing) {
            return response()->json(['message' => 'Data peminjaman tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $borrowing,
        ]);
    }

    /**
     * Pengembalian barang via API.
     *
     * @urlParam borrowing string required UUID dari data peminjaman yang akan dikembalikan. Example: 7b3e1c...
     */
    public function returnItem(\App\Http\Requests\Inventory\Borrowing\ReturnBorrowingRequest $request, Borrowing $borrowing)
    {
        if (Gate::denies('update', $borrowing)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $returnPhotos = [];
            // Handle photos if any (standard file upload)
            if ($request->hasFile('return_photos')) {
                foreach ($request->file('return_photos') as $photo) {
                    $returnPhotos[] = $this->imageOptimizer->optimizeAndSave($photo, 'return_evidence');
                }
            }

            $this->inventoryService->returnBorrowing($borrowing, $request->validated(), $returnPhotos);

            return response()->json([
                'status' => 'success',
                'message' => 'Pengembalian berhasil dicatat',
                'data' => $borrowing->fresh(['sparepart', 'returns']),
            ]);
        } catch (\Exception $e) {
            // Hapus file foto yang terlanjur terupload jika gagal
            foreach ($returnPhotos as $photoPath) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($photoPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($photoPath);
                }
            }

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
