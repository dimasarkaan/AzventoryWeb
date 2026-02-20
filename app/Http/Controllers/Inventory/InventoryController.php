<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use App\Models\User;
use App\Services\InventoryService;
use App\Http\Requests\Inventory\StoreSparepartRequest;
use App\Notifications\MissingPriceNotification;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Traits\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InventoryController extends Controller
{
    use ActivityLogger;

    protected $inventoryService;
    protected $qrCodeService;

    public function __construct(InventoryService $inventoryService, \App\Services\QrCodeService $qrCodeService)
    {
        $this->inventoryService = $inventoryService;
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Menampilkan daftar barang inventaris.
     */
    public function index(Request $request)
    {
        $spareparts = $this->inventoryService->getFilteredSpareparts($request->all(), 10);
        $options = $this->inventoryService->getDropdownOptions();

        return view('inventory.index', array_merge([
            'spareparts' => $spareparts,
        ], $options));
    }

    /**
     * Menampilkan form untuk membuat barang baru.
     */
    public function create()
    {
        $this->authorize('create', Sparepart::class);
        $options = $this->inventoryService->getDropdownOptions();
        return view('inventory.create', $options);
    }

    /**
     * Menyimpan barang baru ke database.
     */
    public function store(\App\Http\Requests\Inventory\StoreSparepartRequest $request)
    {

        $this->authorize('create', Sparepart::class);
        \Illuminate\Support\Facades\Log::info('InventoryController store all: ' . print_r($request->all(), true));
        \Illuminate\Support\Facades\Log::info('InventoryController store validated: ' . print_r($request->validated(), true));

        $result = $this->inventoryService->createSparepart($request->validated());

        if ($result['status'] === 'error_zero_stock') {
            return redirect()->back()->withInput()->with('warning', $result['message']);
        }

        // Jika barang bertipe 'sale' dan harga belum diisi (0 atau null),
        // kirim notifikasi ke semua Superadmin agar segera melengkapi harga.
        if (isset($result['data'])) {
            $sparepart = $result['data'];
            if ($sparepart->type === 'sale' && ($sparepart->price === null || $sparepart->price == 0)) {
                $superadmins = User::where('role', UserRole::SUPERADMIN)->get();
                foreach ($superadmins as $superadmin) {
                    $superadmin->notify(new MissingPriceNotification($sparepart, auth()->user()));
                }
            }
        }

        return redirect()->route('inventory.index')->with('success', $result['message']);
    }

    /**
     * Menampilkan detail barang spesifik.
     */
    public function show(Sparepart $inventory)
    {
        // Ambil data peminjaman dengan paginasi (5 per halaman)
        $borrowings = $inventory->borrowings()
            ->with(['user', 'returns'])
            ->latest()
            ->paginate(5, ['*'], 'history_page');

        // Ambil item serupa dengan paginasi (3 per halaman) 
        $similarItems = Sparepart::where('part_number', $inventory->part_number)
            ->where('id', '!=', $inventory->id)
            ->paginate(3, ['*'], 'similar_page');

        return view('inventory.show', [
            'sparepart' => $inventory,
            'similarItems' => $similarItems,
            'borrowings' => $borrowings
        ]);
    }

    /**
     * Menampilkan form untuk mengedit barang.
     */
    public function edit(Sparepart $inventory)
    {
        $this->authorize('update', $inventory);
        $options = $this->inventoryService->getDropdownOptions();
        return view('inventory.edit', array_merge(['sparepart' => $inventory], $options));
    }

    /**
     * Memperbarui data barang di database.
     */
    public function update(\App\Http\Requests\Inventory\UpdateSparepartRequest $request, Sparepart $inventory)
    {
        $this->authorize('update', $inventory);

        $validated = $request->validated();

        // Cek apakah ada request untuk merge atau keep separate
        $mergeConfirmed = $request->input('merge_confirmed') === 'true';
        $keepSeparate = $request->input('keep_separate') === 'true';

        // Jika belum ada konfirmasi, cek duplikat
        if (!$mergeConfirmed && !$keepSeparate) {
            $duplicateItem = $this->inventoryService->checkUpdateDuplicate($inventory, $validated);

            if ($duplicateItem) {
                // Duplikat ditemukan, redirect kembali dengan modal konfirmasi
                return redirect()->back()
                    ->withInput()
                    ->with('duplicate_detected', true)
                    ->with('duplicate_item', [
                        'id' => $duplicateItem->id,
                        'name' => $duplicateItem->name,
                        'part_number' => $duplicateItem->part_number,
                        'brand' => $duplicateItem->brand,
                        'category' => $duplicateItem->category,
                        'condition' => $duplicateItem->condition,
                        'location' => $duplicateItem->location,
                        'stock' => $duplicateItem->stock,
                        'unit' => $duplicateItem->unit,
                    ])
                    ->with('current_item', [
                        'id' => $inventory->id,
                        'name' => $inventory->name,
                        'part_number' => $inventory->part_number,
                        'stock' => $inventory->stock,
                    ]);
            }
        }

        // Jika user pilih merge
        if ($mergeConfirmed) {
            $duplicateId = $request->input('duplicate_id');
            $duplicateItem = Sparepart::findOrFail($duplicateId);

            $result = $this->inventoryService->mergeSpareparts($inventory, $duplicateItem);

            if ($result['status'] === 'error') {
                return redirect()->route('inventory.edit', $inventory)
                    ->with('error', $result['message']);
            }

            return redirect()->route('inventory.index')
                ->with('success', $result['message']);
        }

        // Normal update (no duplicate atau user pilih keep separate)
        $result = $this->inventoryService->updateSparepart($inventory, $validated);

        return redirect()->route('inventory.index')
            ->with('success', $result['message']);
    }

    /**
     * Menghapus barang dari database (Soft Delete).
     */
    public function destroy($id)
    {
        $inventory = Sparepart::findOrFail($id);
        $this->authorize('delete', $inventory);

        // Check for active borrowings
        if ($inventory->borrowings()->whereIn('status', ['borrowed', 'overdue'])->exists()) {
            return redirect()->back()->with('error', __('ui.error_cannot_delete_borrowed'));
        }

        $result = $this->inventoryService->deleteSparepart($inventory);

        if ($result['status'] === 'error') {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('inventory.index')
            ->with('success', $result['message']);
    }

    public function downloadQrCode(Sparepart $inventory)
    {
        $svgResponse = $this->qrCodeService->generateLabelSvg($inventory);

        $filename = $this->qrCodeService->getLabelFilename($inventory);


        return response($svgResponse, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function printQrCode(Sparepart $inventory)
    {
        if (!$inventory->qr_code_path) {
            abort(404, __('messages.qr_code_not_found'));
        }

        return view('inventory.print_label', ['sparepart' => $inventory]);
    }

    public function checkPartNumber(Request $request)
    {
        $partNumber = $request->query('part_number');
        $sparepart = Sparepart::where('part_number', $partNumber)->first();

        if ($sparepart) {
            return response()->json([
                'exists' => true,
                'data' => [
                    'name' => $sparepart->name,
                    'brand' => $sparepart->brand,
                    'category' => $sparepart->category,
                    'type' => $sparepart->type,
                    'unit' => $sparepart->unit,
                    'price' => $sparepart->price,
                    'image_url' => $sparepart->image ? Storage::url($sparepart->image) : null,
                    'image_path' => $sparepart->image,
                ]
            ]);
        }

        return response()->json(['exists' => false]);
    }

    /**
     * Memulihkan barang yang dihapus (Restore).
     */
    public function restore($id)
    {
        $inventory = Sparepart::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $inventory);

        $result = $this->inventoryService->restoreSparepart($id);

        return redirect()->route('inventory.index', ['trash' => 'true'])
            ->with('success', $result['message']);
    }

    /**
     * Menghapus permanen barang.
     */
    public function forceDelete($id)
    {
        $inventory = Sparepart::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $inventory);

        $result = $this->inventoryService->forceDeleteSparepart($id);

        if ($result['status'] === 'error') {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('inventory.index', ['trash' => 'true'])
            ->with('success', $result['message']);
    }

    /**
     * Menghapus permanen semua barang di sampah.
     */
    public function forceDeleteAll()
    {
        $this->authorize('forceDelete', new Sparepart());
        $result = $this->inventoryService->forceDeleteAllSpareparts();

        return redirect()->route('inventory.index', ['trash' => 'true'])
            ->with($result['status'] === 'empty' ? 'warning' : 'success', $result['message']);
    }

    /**
     * Memulihkan banyak barang sekaligus.
     */
    public function bulkRestore(Request $request)
    {
        $this->authorize('restore', new Sparepart());
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:spareparts,id',
        ]);

        $result = $this->inventoryService->bulkRestore($request->ids);

        if ($result['status'] === 'empty') {
             return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', $result['message']);
    }

    /**
     * Menghapus permanen banyak barang sekaligus.
     */
    public function bulkForceDelete(Request $request)
    {
         $this->authorize('forceDelete', new Sparepart());
         $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:spareparts,id',
        ]);

        $result = $this->inventoryService->bulkForceDelete($request->ids);

        if ($result['status'] === 'empty') {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', $result['message']);
    }
}
