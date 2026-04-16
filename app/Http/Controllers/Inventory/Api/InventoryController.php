<?php

namespace App\Http\Controllers\Inventory\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SparepartCollection;
use App\Http\Resources\SparepartResource;
use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @group Inventory Management
 *
 * API endpoints untuk mengelola katalog, manipulasi stok, dan log stok sparepart.
 */
class InventoryController extends Controller
{
    protected $inventoryService;

    protected $qrCodeService;

    public function __construct(\App\Services\InventoryService $inventoryService, \App\Services\QrCodeService $qrCodeService)
    {
        $this->inventoryService = $inventoryService;
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Mendapatkan daftar barang inventaris.
     *
     * @return mixed
     */
    public function index(Request $request)
    {
        // Ambil data dengan paginasi menggunakan Service (mendukung filter dan pencarian)
        $filters = $request->all();
        $spareparts = $this->inventoryService->getFilteredSpareparts($filters, $request->input('per_page', 20));

        // Mengembalikan dalam format standar Koleksi Resource JSON
        return new SparepartCollection($spareparts);
    }

    /**
     * Menyimpan barang inventaris baru.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', Sparepart::class);

        $validated = $request->validate([
            'part_number' => 'required|unique:spareparts,part_number',
            'name' => 'required|string',
            'brand' => 'required|string',
            'location' => 'required|string',
            'type' => 'required|in:sale,asset',
            'stock' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string',
            'minimum_stock' => 'nullable|integer|min:0',
            'category' => 'required|string',
            'condition' => 'required|string',
            'age' => 'nullable|string|max:50',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $sparepart = Sparepart::create($validated);
        $this->qrCodeService->generate($sparepart);

        return response()->json([
            'status' => 'success',
            'message' => 'Barang baru berhasil ditambahkan',
            'data' => new SparepartResource($sparepart),
        ], 201);
    }

    /**
     * Mendapatkan detail satu barang inventaris.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $sparepart = Sparepart::find($id);

        if (! $sparepart) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Barang tidak ditemukan di katalog.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Detail data barang berhasil diambil',
            'data' => new SparepartResource($sparepart),
        ]);
    }

    /**
     * Memperbarui barang inventaris.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $sparepart = Sparepart::find($id);

        if (! $sparepart) {
            return response()->json(['status' => 'error', 'message' => 'Sparepart not found'], 404);
        }

        $this->authorize('update', $sparepart);

        $validated = $request->validate([
            'part_number' => 'sometimes|unique:spareparts,part_number,'.$id,
            'name' => 'sometimes|string',
            'brand' => 'sometimes|string',
            'location' => 'sometimes|string',
            'type' => 'sometimes|in:sale,asset',
            'stock' => 'sometimes|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string',
            'minimum_stock' => 'nullable|integer|min:0',
            'category' => 'sometimes|string',
            'condition' => 'sometimes|string',
            'age' => 'sometimes|string|max:50',
            'status' => 'sometimes|in:aktif,nonaktif',
        ]);

        $sparepart->update($validated);
        $this->qrCodeService->generate($sparepart->fresh());

        return response()->json([
            'status' => 'success',
            'message' => 'Data Barang berhasil diperbarui',
            'data' => new SparepartResource($sparepart),
        ]);
    }

    /**
     * Menghapus barang inventaris.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $sparepart = Sparepart::find($id);

        if (! $sparepart) {
            return response()->json(['status' => 'error', 'message' => 'Sparepart not found'], 404);
        }

        $this->authorize('delete', $sparepart);

        $sparepart->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data Barang berhasil dihapus secara soft-delete',
        ]);
    }

    /**
     * Menyesuaikan stok (tambah/kurang) untuk penjualan atau pasokan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function adjustStock(Request $request, $id)
    {
        $sparepart = Sparepart::find($id);

        if (! $sparepart) {
            return response()->json(['status' => 'error', 'message' => 'Sparepart not found'], 404);
        }

        $this->authorize('update', $sparepart);

        $request->validate([
            'type' => 'required|in:increment,decrement',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        if ($request->type === 'decrement' && $sparepart->stock < $request->quantity) {
            return response()->json(['status' => 'error', 'message' => 'Insufficient stock'], 400);
        }

        // PENTING: Gunakan $request->user() bukan auth() karena
        // auth() bisa resolve ke guard 'web' (null) saat pakai Sanctum token.
        $apiUser = $request->user();

        if (! $apiUser) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        // Update Stock
        if ($request->type === 'increment') {
            $sparepart->increment('stock', $request->quantity);
        } else {
            $sparepart->decrement('stock', $request->quantity);
        }

        // Log the change
        StockLog::create([
            'sparepart_id' => $sparepart->id,
            'user_id' => $apiUser->id, // FIX: pakai $request->user() bukan auth()->id()
            'type' => $request->type === 'increment' ? 'masuk' : 'keluar',
            'quantity' => $request->quantity,
            'reason' => 'API Adjustment: '.($request->description ?? 'No description'),
            'status' => 'approved',
            'approved_by' => $apiUser->id,
            'approved_at' => now(),
        ]);

        // 1. Broadcast update ke semua user.
        try {
            broadcast(new \App\Events\InventoryUpdatedEvent(
                $sparepart->fresh(),
                'updated',
                $apiUser->name  // FIX: pakai $apiUser bukan auth()->user()
            ))->toOthers();
        } catch (\Exception $e) {
        }

        // 2. Broadcast critical stock alert jika <= minimum.
        if ($sparepart->minimum_stock > 0 && $sparepart->stock <= $sparepart->minimum_stock) {
            $severity = $sparepart->stock === 0 ? 'depleted' : 'critical';
            try {
                broadcast(new \App\Events\StockCriticalEvent($sparepart, $severity));
            } catch (\Exception $e) {
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Stok berhasil disesuaikan',
            'data' => [
                'current_stock' => $sparepart->stock,
                'minimum_stock' => $sparepart->minimum_stock,
                'is_low_stock' => $sparepart->stock <= $sparepart->minimum_stock,
                'part_number' => $sparepart->part_number,
            ],
        ]);
    }

    /**
     * Mendapatkan riwayat mutasi stok untuk barang tertentu.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function logs(Request $request, $id)
    {
        $sparepart = Sparepart::find($id);

        if (! $sparepart) {
            return response()->json(['status' => 'error', 'message' => 'Sparepart not found'], 404);
        }

        $logs = StockLog::where('sparepart_id', $id)
            ->with('user')
            ->latest()
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'status' => 'success',
            'sparepart' => [
                'name' => $sparepart->name,
                'part_number' => $sparepart->part_number,
            ],
            'data' => $logs,
        ]);
    }
}
