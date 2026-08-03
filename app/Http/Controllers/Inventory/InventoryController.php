<?php

// Pengatur lalu lintas utama (Controller) untuk halaman Inventaris (Sparepart).
// Menangani semua alur: dari melihat daftar barang, menambah, mengedit, hingga hapus dan cetak QR Code.
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

    // Menampilkan halaman daftar barang inventaris
    public function index(Request $request)
    {
        // Mengecek apakah user mengakses fitur tempat sampah (trash)
        // Jika bukan Superadmin, maka akses ditolak (403)
        if ($request->boolean('trash') && auth()->user()->role !== UserRole::SUPERADMIN) {
            abort(403, __('Akses tong sampah dibatasi untuk Superadmin.'));
        }

        // Mengambil data barang yang sudah difilter dan dilimit 10 data per halaman
        $spareparts = $this->inventoryService->getFilteredSpareparts($request->all(), 10);
        
        // Mengambil opsi-opsi dropdown untuk filter (seperti kategori, merek, lokasi)
        $options = $this->inventoryService->getDropdownOptions();

        // Menggabungkan data barang dan opsi filter untuk dikirim ke view
        $data = array_merge([
            'spareparts' => $spareparts,
        ], $options);

        // Jika request berupa AJAX atau hanya meminta bagian tabel (untuk fitur live search/filter otomatis)
        if ($request->ajax() || $request->has('table_only')) {
            return response()->json([
                'desktop' => view('inventory.partials.desktop-table', $data)->render(),
                'mobile' => view('inventory.partials.mobile-list', $data)->render(),
                'pagination' => (string) $spareparts->links(),
            ]);
        }

        // Mengembalikan tampilan halaman index dengan membawa data
        return view('inventory.index', $data);
    }

    // Menampilkan halaman form untuk menambahkan barang baru
    public function create()
    {
        // Memastikan pengguna memiliki izin untuk menambahkan data barang
        $this->authorize('create', Sparepart::class);

        // Menampilkan halaman form dengan membawa data opsi dropdown (kategori, dll)
        return view('inventory.create', $this->inventoryService->getDropdownOptions());
    }

    // Menyimpan data barang baru yang diinputkan dari form ke database
    public function store(\App\Http\Requests\Inventory\StoreSparepartRequest $request)
    {
        // Memastikan lagi bahwa pengguna punya izin untuk create
        $this->authorize('create', Sparepart::class);

        // Memanggil service untuk memproses dan menyimpan data barang
        $result = $this->inventoryService->createSparepart($request->validated());

        // Jika barang ditolak karena stok nol (saat ada indikasi duplikasi)
        if ($result['status'] === 'error_zero_stock') {
            return redirect()->back()->withInput()->with('warning', $result['message']);
        }

        // Otomatis mengirim notifikasi jika barang yang ditambahkan itu bertipe jual tapi harganya belum diisi
        if (isset($result['data'])) {
            $sparepart = $result['data'];
            if ($sparepart->type === 'sale' && ($sparepart->price === null || $sparepart->price == 0)) {
                // Mengambil semua user yang berstatus Superadmin
                $superadmins = User::where('role', UserRole::SUPERADMIN)->get();
                /** @var User $superadmin */
                foreach ($superadmins as $superadmin) {
                    $superadmin->notify(new MissingPriceNotification($sparepart, auth()->user()));
                }
            }
        }

        // Kembali ke halaman daftar barang dengan pesan sukses
        return redirect()->route('inventory.index')->with('success', $result['message']);
    }

    // Menampilkan detail informasi barang beserta riwayat peminjamannya
    public function show(Sparepart $inventory)
    {
        // Me-load data relasi (kategori, merek, lokasi) sekaligus agar tidak error saat ditampilkan (Lazy Loading)
        $inventory->load(['category', 'brand', 'location']);

        // Mengambil riwayat peminjaman barang ini dan diurutkan dari yang terbaru
        $borrowingQuery = $inventory->borrowings()
            ->with(['user', 'returns'])
            ->withSum('returns', 'quantity')
            ->latest();

        // Jika yang login adalah Operator, batasi agar dia hanya bisa melihat riwayat peminjamannya sendiri
        if (auth()->user()->role === UserRole::OPERATOR) {
            $borrowingQuery->where('user_id', auth()->id());
        }

        // Membagi riwayat peminjaman menjadi 5 data per halaman
        $borrowings = $borrowingQuery->paginate(5, ['*'], 'history_page')->withQueryString();

        // Mencari barang lain yang sejenis (berdasarkan Part Number yang sama) untuk mempermudah perbandingan stok
        $similarItems = Sparepart::with(['brand', 'category', 'location'])
            ->where('part_number', $inventory->part_number)
            ->where('id', '!=', $inventory->id) // Kecualikan barang yang sedang dilihat ini
            ->paginate(3, ['*'], 'similar_page')->withQueryString();

        // Menampilkan halaman detail barang dengan membawa semua data yang sudah disiapkan
        return view('inventory.show', [
            'sparepart' => $inventory,
            'similarItems' => $similarItems,
            'borrowings' => $borrowings,
        ]);
    }

    // Menampilkan halaman form untuk mengedit data barang
    public function edit(Sparepart $inventory)
    {
        // Mengecek apakah user diizinkan untuk mengedit data barang
        $this->authorize('update', $inventory);
        
        // Me-load relasi agar nama kategori/brand/lokasi bisa ditampilkan dengan baik di form
        $inventory->load(['category', 'brand', 'location']);
        
        // Mengambil data pilihan dropdown untuk form edit
        $options = $this->inventoryService->getDropdownOptions();

        // Mengirimkan data barang beserta pilihan dropdown ke view form edit
        return view('inventory.edit', array_merge(['sparepart' => $inventory], $options));
    }

    // Memperbarui data barang yang sudah diedit dan mengecek potensi duplikasi
    public function update(\App\Http\Requests\Inventory\UpdateSparepartRequest $request, Sparepart $inventory)
    {
        // Memastikan izin untuk mengupdate
        $this->authorize('update', $inventory);

        $validated = $request->validated();
        
        // Mengecek apakah user memilih untuk menggabungkan atau memisahkan barang jika terdeteksi duplikat
        $mergeConfirmed = $request->input('merge_confirmed') === 'true';
        $keepSeparate = $request->input('keep_separate') === 'true';

        // Jika user belum memilih tindakan apa pun, kita validasi dulu apakah ada indikasi duplikasi
        if (! $mergeConfirmed && ! $keepSeparate) {
            $duplicateItem = $this->inventoryService->checkUpdateDuplicate($inventory, $validated);

            if ($duplicateItem) {
                $duplicateItem->load(['brand', 'category', 'location']);

                // Jika terdeteksi duplikat, kembalikan ke form edit dan tampilkan modal konfirmasi
                // sambil membawa data barang duplikat tersebut agar user bisa melihat perbandingannya
                return redirect()->back()
                    ->withInput()
                    ->with('duplicate_detected', true)
                    ->with('duplicate_item', [
                        'id' => $duplicateItem->id,
                        'name' => $duplicateItem->name,
                        'part_number' => $duplicateItem->part_number,
                        'brand' => $duplicateItem->brand->name ?? '-',
                        'category' => $duplicateItem->category->name ?? '-',
                        'condition' => $duplicateItem->condition,
                        'location' => $duplicateItem->location->name ?? '-',
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

        // Jika user mengkonfirmasi untuk menggabungkan (merge) dengan barang duplikat yang ada
        if ($mergeConfirmed) {
            $duplicateItem = Sparepart::findOrFail($request->input('duplicate_id'));
            
            // Panggil service untuk memproses penggabungan stok dan penghapusan barang sumber
            $result = $this->inventoryService->mergeSpareparts($inventory, $duplicateItem);

            // Jika gagal digabung (misal sedang dipinjam), kembalikan dengan pesan error
            if ($result['status'] === 'error') {
                return redirect()->route('inventory.edit', $inventory)->with('error', $result['message']);
            }

            return redirect()->route('inventory.index')->with('success', $result['message']);
        }

        // Jika tidak ada duplikasi atau user memilih untuk membiarkannya tetap terpisah, lakukan update biasa
        $result = $this->inventoryService->updateSparepart($inventory, $validated);

        // Kembali ke halaman daftar dengan pesan berhasil
        return redirect()->route('inventory.index')->with('success', $result['message']);
    }

    // Menghapus data barang ke tempat sampah (Soft Delete)
    public function destroy(Sparepart $inventory)
    {
        // Pengecekan izin akses delete
        $this->authorize('delete', $inventory);

        // Mencegah barang dihapus jika masih ada user yang meminjamnya (status borrowed/overdue)
        if ($inventory->borrowings()->whereIn('status', ['borrowed', 'overdue'])->exists()) {
            return redirect()->back()->with('error', __('ui.error_cannot_delete_borrowed'));
        }

        // Memanggil service untuk mengeksekusi penghapusan barang
        $result = $this->inventoryService->deleteSparepart($inventory);

        // Mengarahkan kembali ke daftar barang dengan notifikasi sukses
        return redirect()->route('inventory.index')->with('success', $result['message']);
    }

    // Mengunduh label QR Code barang dalam format gambar vektor (SVG)
    public function downloadQrCode(Sparepart $inventory)
    {
        // Mem-generate respon file SVG dari service
        $svgResponse = $this->qrCodeService->generateLabelSvg($inventory);
        $filename = $this->qrCodeService->getLabelFilename($inventory);

        // Mengembalikan balikan (response) agar browser men-download file tersebut
        return response($svgResponse, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    // Membuka tab baru untuk mencetak satu label QR Code barang
    public function printQrCode(Sparepart $inventory)
    {
        // Validasi apakah file QR code-nya benar-benar ada di storage server
        if (! $inventory->qr_code_path || ! Storage::disk('public')->exists($inventory->qr_code_path)) {
            abort(404, __('messages.qr_code_not_found'));
        }

        // Tampilkan halaman khusus cetak label
        return view('inventory.print_label', ['sparepart' => $inventory]);
    }

    // Membuka halaman khusus untuk mencetak banyak label QR Code sekaligus (Bulk Print)
    public function bulkPrintQrCode(Request $request)
    {
        // Mencegah Operator mengakses fitur cetak massal
        if (auth()->user()->role === UserRole::OPERATOR) {
            abort(403, 'Operator tidak memiliki akses untuk cetak massal.');
        }

        $ids = $request->query('ids');

        // Mengubah parameter string id yang dipisahkan koma menjadi bentuk array
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        // Jika tidak ada ID yang dikirim, kembalikan dengan pesan peringatan
        if (empty($ids)) {
            return redirect()->route('inventory.index')->with('warning', 'Pilih minimal satu item untuk dicetak.');
        }

        // Membatasi jumlah maksimal cetak demi menjaga performa server/browser
        if (count($ids) > 100) {
            return redirect()->back()->with('error', 'Maksimal 100 item untuk satu sesi cetak (keamanan performa).');
        }

        // Mengambil data barang dan menyaring hanya barang yang benar-benar memiliki file QR Code di storage
        $spareparts = Sparepart::whereIn('id', $ids)->get()->filter(function ($item) {
            return $item->qr_code_path && Storage::disk('public')->exists($item->qr_code_path);
        });

        // Jika setelah disaring ternyata kosong, tampilkan error 404
        if ($spareparts->isEmpty()) {
            abort(404, 'Item tidak ditemukan.');
        }

        // Tampilkan halaman cetak label massal
        return view('inventory.bulk_print_label', ['spareparts' => $spareparts]);
    }

    // Menghapus banyak item sekaligus ke tempat sampah (Soft Delete)
    public function bulkDestroy(Request $request)
    {
        // Mengecek secara manual apakah pengguna ini boleh menghapus Sparepart
        if ($request->user()->cannot('delete', new Sparepart)) {
            return response()->json(['message' => __('Hanya Superadmin yang memiliki izin untuk menghapus barang.')], 403);
        }

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:spareparts,id',
        ]);

        $ids = $request->input('ids', []);

        // Validasi jika array ID kosong
        if (empty($ids)) {
            return response()->json(['message' => 'Pilih minimal satu item.'], 400);
        }

        // Menghitung apakah dari barang-barang yang dipilih ada yang status peminjamannya masih aktif/telat
        $borrowedItemsCount = Sparepart::whereIn('id', $ids)
            ->whereHas('borrowings', function ($query) {
                $query->whereIn('status', ['borrowed', 'overdue']);
            })->count();

        // Jika ada barang yang sedang dipinjam, tolak proses hapus massal ini
        if ($borrowedItemsCount > 0) {
            return response()->json(['message' => 'Beberapa item tidak dapat dihapus karena masih sedang dipinjam.'], 422);
        }

        $count = count($ids);
        $spareparts = Sparepart::whereIn('id', $ids)->get();
        $names = [];
        foreach($spareparts as $sparepart) {
            $names[] = $sparepart->part_number . ' - ' . $sparepart->name;
        }
        $namesList = implode(', ', $names);
        
        // Mencatat aktivitas penghapusan massal ke dalam log
        $this->logActivity('Hapus Massal (Soft)', "Menghapus {$count} item inventaris ke tong sampah.", [
            'items' => ['old' => $namesList, 'new' => '-']
        ]);

        // Mengeksekusi query untuk menghapus item-item tersebut (soft delete)
        Sparepart::whereIn('id', $ids)->delete();

        // Membersihkan cache agar perubahan langsung terlihat
        $this->inventoryService->clearCache();

        // Mengirim respon sukses kembali ke AJAX/Frontend
        return response()->json(['message' => 'Berhasil menghapus '.$count.' item.']);
    }

    // Mencatat rekam jejak aktivitas pencetakan label QR ke log sistem
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

        $spareparts = Sparepart::whereIn('id', $ids)->get();
        $names = [];
        foreach($spareparts as $sparepart) {
            $names[] = $sparepart->part_number . ' - ' . $sparepart->name;
        }
        $namesList = implode(', ', $names);

        $this->logActivity(
            'Cetak Label',
            'Mencetak total '.$total.' label untuk '.count($ids).' item inventaris.',
            [
                'items' => ['old' => '-', 'new' => $namesList],
                'counts' => ['old' => '-', 'new' => json_encode($counts)],
                'total_labels' => ['old' => '-', 'new' => $total],
            ]
        );

        return response()->json(['status' => 'success']);
    }

    // Endpoint API untuk mengecek otomatis apakah suatu barang sudah ada berdasarkan Part Number
    public function checkPartNumber(Request $request)
    {
        $partNumber = $request->query('part_number');
        $sparepart = Sparepart::with(['brand', 'category', 'location'])->where('part_number', $partNumber)->first();

        // Jika barang ditemukan, kembalikan data barang tersebut ke frontend agar bisa di-autofill
        if ($sparepart) {
            return response()->json([
                'exists' => true,
                'data' => [
                    'name' => $sparepart->name,
                    'brand' => $sparepart->brand->name ?? '',
                    'category' => $sparepart->category->name ?? '',
                    'type' => $sparepart->type,
                    'unit' => $sparepart->unit,
                    'price' => $sparepart->price,
                    'image_url' => $sparepart->image ? Storage::url($sparepart->image) : null,
                    'image_path' => $sparepart->image,
                    'brand_id' => $sparepart->brand_id,
                    'category_id' => $sparepart->category_id,
                    'location_id' => $sparepart->location_id,
                ],
            ]);
        }

        return response()->json(['exists' => false]);
    }

    // Memulihkan barang yang sebelumnya ada di tempat sampah agar aktif kembali
    public function restore($id)
    {
        $inventory = Sparepart::onlyTrashed()->where('uuid', $id)->firstOrFail();
        $this->authorize('restore', $inventory);

        $result = $this->inventoryService->restoreSparepart($inventory->id);

        return redirect()->route('inventory.index', ['trash' => 'true'])->with('success', $result['message']);
    }

    // Menghapus barang selamanya dari database (Permanen)
    public function forceDelete($id)
    {
        $inventory = Sparepart::onlyTrashed()->where('uuid', $id)->firstOrFail();
        $this->authorize('forceDelete', $inventory);

        $result = $this->inventoryService->forceDeleteSparepart($inventory->id);

        if ($result['status'] === 'error') {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('inventory.index', ['trash' => 'true'])->with('success', $result['message']);
    }

    // Mengosongkan seluruh barang yang ada di tempat sampah secara permanen
    public function forceDeleteAll()
    {
        $this->authorize('forceDelete', new Sparepart);
        $result = $this->inventoryService->forceDeleteAllSpareparts();

        return redirect()->route('inventory.index', ['trash' => 'true'])
            ->with($result['status'] === 'empty' ? 'warning' : 'success', $result['message']);
    }

    // Memulihkan banyak barang sekaligus dari tempat sampah
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

    // Menghapus banyak barang secara permanen sekaligus dari tempat sampah
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
