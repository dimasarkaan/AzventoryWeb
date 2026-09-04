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
 * @group Manajemen Inventaris
 *
 * Ini adalah fungsi inti aplikasi untuk mengelola barang.
 *
 * Anda bisa menggunakan API ini untuk menambah daftar barang baru, melihat stok, memperbarui informasi barang, serta mencatat riwayat barang masuk atau keluar.
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
    public function store(\App\Http\Requests\Inventory\StoreSparepartRequest $request)
    {
        $result = $this->inventoryService->createSparepart($request->validated());

        if ($result['status'] === 'error_zero_stock') {
            return response()->json([
                'status' => 'error',
                'message' => $result['message'],
            ], 422);
        }

        if (isset($result['data'])) {
            $sparepart = $result['data'];
            if ($sparepart->type === 'sale' && ($sparepart->price === null || $sparepart->price == 0)) {
                $superadmins = User::where('role', \App\Enums\UserRole::SUPERADMIN)->get();
                foreach ($superadmins as $superadmin) {
                    $superadmin->notify(new \App\Notifications\MissingPriceNotification($sparepart, auth()->user()));
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => $result['message'] ?? 'Barang baru berhasil ditambahkan',
            'data' => new SparepartResource($result['data']),
        ], 201);
    }

    /**
     * Mendapatkan detail satu barang inventaris.
     *
     * @urlParam inventory string required UUID dari barang inventaris. Example: 9a5f3b...
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Sparepart $inventory)
    {
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
     * @urlParam inventory string required UUID dari barang inventaris yang akan diupdate. Example: 9a5f3b...
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(\App\Http\Requests\Inventory\UpdateSparepartRequest $request, Sparepart $inventory)
    {
        $result = $this->inventoryService->updateSparepart($inventory, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Data Barang berhasil diperbarui',
            'data' => new SparepartResource($result['data']),
        ]);
    }

    /**
     * Menghapus barang inventaris.
     *
     * @urlParam inventory string required UUID dari barang inventaris. Example: 9a5f3b...
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Sparepart $inventory)
    {
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
     *
     * @bodyParam quantity integer required Jumlah penyesuaian stok (bisa minus). Example: 5
     * @bodyParam notes string required Alasan penyesuaian stok. Example: Penambahan stok baru
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
            $this->logActivity("{$actionWord} Stok API", "{$actionWord} {$request->quantity} unit untuk barang '{$lockedSparepart->name}' via API. Alasan: ".($request->description ?? '-'));

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
