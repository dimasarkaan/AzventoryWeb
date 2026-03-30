<?php

namespace App\Http\Controllers\Inventory;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use App\Models\User;
use App\Notifications\MissingPriceNotification;
use App\Services\InventoryService;
use App\Services\QrCodeService;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    use ActivityLogger;

    protected $inventoryService;

    protected $qrCodeService;

    public function __construct(InventoryService $inventoryService, QrCodeService $qrCodeService)
    {
        $this->inventoryService = $inventoryService;
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Menampilkan daftar barang inventaris dengan dukungan filter dan paginasi.
     */
    public function index(Request $request)
    {
        if ($request->has('trash') && auth()->user()->role !== UserRole::SUPERADMIN) {
            abort(403, __('Akses tong sampah dibatasi untuk Superadmin.'));
        }

        $spareparts = $this->inventoryService->getFilteredSpareparts($request->all(), 10);
        $options = $this->inventoryService->getDropdownOptions();

        $data = array_merge([
            'spareparts' => $spareparts,
        ], $options);

        // Dukungan untuk pembaruan real-time via AJAX (Partial Refresh)
        if ($request->ajax() || $request->has('table_only')) {
            return response()->json([
                'desktop' => view('inventory.partials.desktop-table', $data)->render(),
                'mobile' => view('inventory.partials.mobile-list', $data)->render(),
                'pagination' => (string) $spareparts->links(),
            ]);
        }

        return view('inventory.index', $data);
    }

    /**
     * Menampilkan form pendaftaran barang baru.
     */
    public function create()
    {
        $this->authorize('create', Sparepart::class);

        return view('inventory.create', $this->inventoryService->getDropdownOptions());
    }

    /**
     * Menyimpan data barang baru dan mengirimkan notifikasi jika harga belum lengkap.
     */
    public function store(\App\Http\Requests\Inventory\StoreSparepartRequest $request)
    {
        $this->authorize('create', Sparepart::class);

        $result = $this->inventoryService->createSparepart($request->validated());

        if ($result['status'] === 'error_zero_stock') {
            return redirect()->back()->withInput()->with('warning', $result['message']);
        }

        // Otomatisasi notifikasi jika barang bertipe jual namun harganya kosong
        if (isset($result['data'])) {
            $sparepart = $result['data'];
            if ($sparepart->type === 'sale' && ($sparepart->price === null || $sparepart->price == 0)) {
                $superadmins = User::where('role', UserRole::SUPERADMIN)->get();
                /** @var User $superadmin */
                foreach ($superadmins as $superadmin) {
                    $superadmin->notify(new MissingPriceNotification($sparepart, auth()->user()));
                }
            }
        }

        return redirect()->route('inventory.index')->with('success', $result['message']);
    }

    /**
     * Menampilkan detail informasi barang beserta riwayat peminjaman.
     */
    public function show(Sparepart $inventory)
    {
        $borrowingQuery = $inventory->borrowings()
            ->with(['user', 'returns'])
            ->withSum('returns', 'quantity')
            ->latest();

        // Operator hanya diizinkan melihat riwayat peminjamannya sendiri
        if (auth()->user()->role === UserRole::OPERATOR) {
            $borrowingQuery->where('user_id', auth()->id());
        }

        $borrowings = $borrowingQuery->paginate(5, ['*'], 'history_page');

        // Mengambil aset serupa (berdasarkan Part Number) untuk memudahkan manajemen stok
        $similarItems = Sparepart::where('part_number', $inventory->part_number)
            ->where('id', '!=', $inventory->id)
            ->paginate(3, ['*'], 'similar_page');

        return view('inventory.show', [
            'sparepart' => $inventory,
            'similarItems' => $similarItems,
            'borrowings' => $borrowings,
        ]);
    }

    /**
     * Menampilkan form penyuntingan data barang.
     */
    public function edit(Sparepart $inventory)
    {
        $this->authorize('update', $inventory);
        $options = $this->inventoryService->getDropdownOptions();

        return view('inventory.edit', array_merge(['sparepart' => $inventory], $options));
    }

    /**
     * Memperbarui data barang dengan deteksi potensi duplikasi data.
     */
    public function update(\App\Http\Requests\Inventory\UpdateSparepartRequest $request, Sparepart $inventory)
    {
        $this->authorize('update', $inventory);

        $validated = $request->validated();
        $mergeConfirmed = $request->input('merge_confirmed') === 'true';
        $keepSeparate = $request->input('keep_separate') === 'true';

        // Validasi duplikasi sebelum menyimpan perubahan
        if (! $mergeConfirmed && ! $keepSeparate) {
            $duplicateItem = $this->inventoryService->checkUpdateDuplicate($inventory, $validated);

            if ($duplicateItem) {
                // Return data duplikat untuk memicu modal konfirmasi di frontend
                return redirect()->back()
                    ->withInput()
                    ->with('duplicate_detected', true)
                    ->with('duplicate_item', [
                        'id' => $duplicateItem->id, 'name' => $duplicateItem->name,
                        'part_number' => $duplicateItem->part_number, 'brand' => $duplicateItem->brand,
                        'category' => $duplicateItem->category, 'condition' => $duplicateItem->condition,
                        'location' => $duplicateItem->location, 'stock' => $duplicateItem->stock,
                        'unit' => $duplicateItem->unit,
                    ])
                    ->with('current_item', [
                        'id' => $inventory->id, 'name' => $inventory->name,
                        'part_number' => $inventory->part_number, 'stock' => $inventory->stock,
                    ]);
            }
        }

        if ($mergeConfirmed) {
            $duplicateItem = Sparepart::findOrFail($request->input('duplicate_id'));
            $result = $this->inventoryService->mergeSpareparts($inventory, $duplicateItem);

            if ($result['status'] === 'error') {
                return redirect()->route('inventory.edit', $inventory)->with('error', $result['message']);
            }

            return redirect()->route('inventory.index')->with('success', $result['message']);
        }

        $result = $this->inventoryService->updateSparepart($inventory, $validated);

        return redirect()->route('inventory.index')->with('success', $result['message']);
    }

    /**
     * Menghapus barang (Soft Delete).
     */
    public function destroy($id)
    {
        $inventory = Sparepart::findOrFail($id);
        $this->authorize('delete', $inventory);

        if ($inventory->borrowings()->whereIn('status', ['borrowed', 'overdue'])->exists()) {
            return redirect()->back()->with('error', __('ui.error_cannot_delete_borrowed'));
        }

        $result = $this->inventoryService->deleteSparepart($inventory);

        return redirect()->route('inventory.index')->with('success', $result['message']);
    }

    /**
     * Mengunduh label QR dalam format SVG.
     */
    public function downloadQrCode(Sparepart $inventory)
    {
        $svgResponse = $this->qrCodeService->generateLabelSvg($inventory);
        $filename = $this->qrCodeService->getLabelFilename($inventory);

        return response($svgResponse, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Menampilkan halaman khusus untuk pencetakan label QR.
     */
    public function printQrCode(Sparepart $inventory)
    {
        if (! $inventory->qr_code_path || ! Storage::disk('public')->exists($inventory->qr_code_path)) {
            abort(404, __('messages.qr_code_not_found'));
        }

        return view('inventory.print_label', ['sparepart' => $inventory]);
    }

    /**
     * Menampilkan halaman khusus untuk pencetakan banyak label QR sekaligus.
     */
    public function bulkPrintQrCode(Request $request)
    {
        if (auth()->user()->role === UserRole::OPERATOR) {
            abort(403, 'Operator tidak memiliki akses untuk cetak massal.');
        }

        $ids = $request->query('ids');

        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        if (empty($ids)) {
            return redirect()->route('inventory.index')->with('warning', 'Pilih minimal satu item untuk dicetak.');
        }

        if (count($ids) > 100) {
            return redirect()->back()->with('error', 'Maksimal 100 item untuk satu sesi cetak (keamanan performa).');
        }

        $spareparts = Sparepart::whereIn('id', $ids)->get()->filter(function ($item) {
            return $item->qr_code_path && Storage::disk('public')->exists($item->qr_code_path);
        });

        if ($spareparts->isEmpty()) {
            abort(404, 'Item tidak ditemukan.');
        }

        return view('inventory.bulk_print_label', ['spareparts' => $spareparts]);
    }

    /**
     * Menghapus banyak item sekaligus (Soft Delete).
     */
    public function bulkDestroy(Request $request)
    {
        if (auth()->user()->role === UserRole::OPERATOR) {
            return response()->json(['message' => 'Operator tidak memiliki izin untuk menghapus barang.'], 403);
        }

        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json(['message' => 'Pilih minimal satu item.'], 400);
        }

        $count = count($ids);
        $this->logActivity('Hapus Massal (Soft)', "Menghapus {$count} item inventaris ke tong sampah.", ['ids' => $ids]);

        Sparepart::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Berhasil menghapus '.$count.' item.']);
    }

    /**
     * Mencatat aktivitas pencetakan ke log.
     */
    public function logPrintActivity(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'counts' => 'required|array',
            'total' => 'required|integer',
        ]);

        $ids = $request->input('ids');
        $counts = $request->input('counts');
        $total = $request->input('total');

        $this->logActivity(
            'Cetak Label',
            'Mencetak total '.$total.' label untuk '.count($ids).' item inventaris.',
            [
                'item_ids' => $ids,
                'counts' => $counts,
                'total_labels' => $total,
            ]
        );

        return response()->json(['status' => 'success']);
    }

    /**
     * Endpoint API untuk pengecekan otomatis keberadaan aset berdasarkan Part Number.
     */
    public function checkPartNumber(Request $request)
    {
        $partNumber = $request->query('part_number');
        $sparepart = Sparepart::where('part_number', $partNumber)->first();

        if ($sparepart) {
            return response()->json([
                'exists' => true,
                'data' => [
                    'name' => $sparepart->name, 'brand' => $sparepart->brand,
                    'category' => $sparepart->category, 'type' => $sparepart->type,
                    'unit' => $sparepart->unit, 'price' => $sparepart->price,
                    'image_url' => $sparepart->image ? Storage::url($sparepart->image) : null,
                    'image_path' => $sparepart->image,
                ],
            ]);
        }

        return response()->json(['exists' => false]);
    }

    /**
     * Memulihkan aset yang sebelumnya telah dihapus secara lunak.
     */
    public function restore($id)
    {
        $inventory = Sparepart::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $inventory);

        $result = $this->inventoryService->restoreSparepart($id);

        return redirect()->route('inventory.index', ['trash' => 'true'])->with('success', $result['message']);
    }

    /**
     * Menghapus aset dari database secara permanen.
     */
    public function forceDelete($id)
    {
        $inventory = Sparepart::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $inventory);

        $result = $this->inventoryService->forceDeleteSparepart($id);

        if ($result['status'] === 'error') {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('inventory.index', ['trash' => 'true'])->with('success', $result['message']);
    }

    /**
     * Menghapus seluruh aset di tong sampah secara permanen.
     */
    public function forceDeleteAll()
    {
        $this->authorize('forceDelete', new Sparepart);
        $result = $this->inventoryService->forceDeleteAllSpareparts();

        return redirect()->route('inventory.index', ['trash' => 'true'])
            ->with($result['status'] === 'empty' ? 'warning' : 'success', $result['message']);
    }

    /**
     * Memulihkan banyak aset sekaligus dari tong sampah.
     */
    public function bulkRestore(Request $request)
    {
        $this->authorize('restore', new Sparepart);
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:spareparts,id',
        ]);

        $result = $this->inventoryService->bulkRestore($request->ids);

        return redirect()->back()->with($result['status'] === 'empty' ? 'error' : 'success', $result['message']);
    }

    /**
     * Menghapus banyak aset secara permanen sekaligus.
     */
    public function bulkForceDelete(Request $request)
    {
        $this->authorize('forceDelete', new Sparepart);
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:spareparts,id',
        ]);

        $result = $this->inventoryService->bulkForceDelete($request->ids);

        return redirect()->back()->with($result['status'] === 'empty' ? 'error' : 'success', $result['message']);
    }
}
