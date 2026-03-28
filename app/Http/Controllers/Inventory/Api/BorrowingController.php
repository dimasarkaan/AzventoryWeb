<?php

namespace App\Http\Controllers\Inventory\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Services\InventoryService;
use App\Services\ImageOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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

        $borrowings = $query->latest()->paginate($request->input('per_page', 20));

        return response()->json($borrowings);
    }

    /**
     * Mencatat peminjaman baru via API.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sparepart_id' => 'required|exists:spareparts,id',
            'quantity' => 'required|integer|min:1',
            'borrower_name' => 'required|string', // Untuk catatan tambahan
            'expected_return_at' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);

        $sparepart = Sparepart::findOrFail($validated['sparepart_id']);

        // Cek otorisasi
        if (Gate::denies('create', Borrowing::class)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            // Kita pakai data dari request untuk memenuhi parameter service
            $serviceData = [
                'borrower_name' => $validated['borrower_name'],
                'quantity' => $validated['quantity'],
                'expected_return_at' => $validated['expected_return_at'],
                'notes' => $validated['notes'] ?? '',
                'user_id' => $request->user()->id,
            ];

            $borrowing = $this->inventoryService->createBorrowing($sparepart, $serviceData);

            return response()->json([
                'status' => 'success',
                'message' => 'Peminjaman berhasil dicatat via API',
                'data' => $borrowing->load(['sparepart', 'user'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Detail peminjaman.
     */
    public function show($id)
    {
        $borrowing = Borrowing::with(['user', 'sparepart', 'returns'])->find($id);

        if (!$borrowing) {
            return response()->json(['message' => 'Data peminjaman tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $borrowing
        ]);
    }

    /**
     * Pengembalian barang via API.
     */
    public function returnItem(Request $request, $id)
    {
        $borrowing = Borrowing::findOrFail($id);

        if (Gate::denies('update', $borrowing)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $borrowing->quantity,
            'condition' => 'required|string',
            'notes' => 'nullable|string',
            // Photos handling via API might be tricky depending on client, 
            // but we'll support base64 or file if provided.
        ]);

        try {
            $returnPhotos = [];
            // Handle photos if any (standard file upload)
            if ($request->hasFile('return_photos')) {
                foreach ($request->file('return_photos') as $photo) {
                    $returnPhotos[] = $this->imageOptimizer->optimizeAndSave($photo, 'return_evidence');
                }
            }

            // Map API keys to Service keys
            $serviceData = [
                'return_quantity' => $validated['quantity'],
                'return_condition' => $validated['condition'],
                'return_notes' => $validated['notes'] ?? '',
            ];

            $this->inventoryService->returnBorrowing($borrowing, $serviceData, $returnPhotos);

            return response()->json([
                'status' => 'success',
                'message' => 'Pengembalian berhasil dicatat',
                'data' => $borrowing->fresh(['sparepart', 'returns'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
