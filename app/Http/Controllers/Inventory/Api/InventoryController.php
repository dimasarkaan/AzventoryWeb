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
    use \App\Traits\ActivityLogger;
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
            'brand_id' => 'required|exists:brands,id',
            'location_id' => 'required|exists:locations,id',
            'type' => 'required|in:sale,asset',
            'stock' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string',
            'minimum_stock' => 'nullable|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|string',
            'age' => 'nullable|string|max:50',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $sparepart = Sparepart::create($validated);
        $this->qrCodeService->generate($sparepart);

        $this->logActivity('Barang Dibuat (API)', "Barang baru '{$sparepart->name}' ditambahkan melalui API.");

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
        $inventory = Sparepart::where('uuid', $id)->first();
        if (! $inventory) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Barang tidak ditemukan di katalog.',
            ], 404);
        }

        $inventory->load(['brand', 'category', 'location']);

        return response()->json([
            'status' => 'success',
            'message' => 'Detail data barang berhasil diambil',
            'data' => new SparepartResource($inventory),
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
        $inventory = Sparepart::where('uuid', $id)->first();
        if (! $inventory) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Barang tidak ditemukan di katalog.',
            ], 404);
        }

        $this->authorize('update', $inventory);

        $validated = $request->validate([
            'part_number' => 'sometimes|unique:spareparts,part_number,'.$inventory->id,
            'name' => 'sometimes|string',
            'brand_id' => 'sometimes|exists:brands,id',
            'location_id' => 'sometimes|exists:locations,id',
            'type' => 'sometimes|in:sale,asset',
            'stock' => 'sometimes|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string',
            'minimum_stock' => 'nullable|integer|min:0',
            'category_id' => 'sometimes|exists:categories,id',
            'condition' => 'sometimes|string',
            'age' => 'sometimes|string|max:50',
            'status' => 'sometimes|in:aktif,nonaktif',
        ]);

        $inventory->update($validated);
        $inventory->load(['brand', 'category', 'location']);
        $this->qrCodeService->generate($inventory);

        $this->logActivity('Barang Diupdate (API)', "Data barang '{$inventory->name}' diperbarui melalui API.");

        return response()->json([
            'status' => 'success',
            'message' => 'Data Barang berhasil diperbarui',
            'data' => new SparepartResource($inventory),
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
        $inventory = Sparepart::where('uuid', $id)->first();
        if (! $inventory) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data Barang tidak ditemukan di katalog.',
            ], 404);
        }

        $this->authorize('delete', $inventory);

        $this->logActivity('Barang Dihapus (API)', "Barang '{$inventory->name}' dihapus melalui API.");
        $inventory->delete();

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
        $sparepart = Sparepart::where('uuid', $id)->first();

        if (! $sparepart) {
            return response()->json(['status' => 'error', 'message' => 'Data Barang tidak ditemukan di katalog.'], 404);
        }

        // Fix Role-Based Access for API (Only Superadmin and Admin)
        $this->authorize('update', $sparepart);

        $request->validate([
            'type' => 'required|in:increment,decrement',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
        ]);

        $apiUser = $request->user();

        if (! $apiUser) {
            return response()->json(['status' => 'error', 'message' => 'Unauthenticated'], 401);
        }

        // Bungkus dengan Transaction dan DB Lock (Pessimistic Locking)
        $result = \Illuminate\Support\Facades\DB::transaction(function () use ($id, $request, $apiUser) {
            // Mengunci baris ini untuk mencegah race condition dari scanner lain
            $lockedSparepart = Sparepart::where('uuid', $id)->lockForUpdate()->first();

            if ($request->type === 'decrement' && $lockedSparepart->stock < $request->quantity) {
                return ['status' => 'error', 'message' => 'Insufficient stock', 'code' => 400];
            }

            // Update Stock aman di dalam lock
            if ($request->type === 'increment') {
                $lockedSparepart->stock += $request->quantity;
            } else {
                $lockedSparepart->stock -= $request->quantity;
            }
            $lockedSparepart->save();

            // Log the change
            StockLog::create([
                'sparepart_id' => $lockedSparepart->id,
                'user_id' => $apiUser->id,
                'type' => $request->type === 'increment' ? 'masuk' : 'keluar',
                'quantity' => $request->quantity,
                'reason' => 'API Adjustment: '.($request->description ?? 'No description'),
                'status' => 'approved',
                'approved_by' => $apiUser->id,
                'approved_at' => now(),
            ]);
            
            $actionWord = $request->type === 'increment' ? 'Penambahan' : 'Pengurangan';
            $this->logActivity("{$actionWord} Stok API", "{$actionWord} {$request->quantity} unit untuk barang '{$lockedSparepart->name}' via API. Alasan: " . ($request->description ?? '-'));

            return ['status' => 'success', 'data' => $lockedSparepart];
        });

        if ($result['status'] === 'error') {
            return response()->json(['status' => 'error', 'message' => $result['message']], $result['code']);
        }

        $sparepart = $result['data'];

        // 1. Broadcast update ke semua user.
        try {
            broadcast(new \App\Events\InventoryUpdatedEvent(
                $sparepart,
                'updated',
                $apiUser->name
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
                'is_low_stock' => $sparepart->isLowStock(),
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
        $sparepart = Sparepart::where('uuid', $id)->first();

        if (! $sparepart) {
            return response()->json(['status' => 'error', 'message' => 'Sparepart not found'], 404);
        }

        $logs = StockLog::where('sparepart_id', $sparepart->id)
            ->with('user')
            ->latest()
            ->paginate($request->input('per_page', 20))->withQueryString();

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
