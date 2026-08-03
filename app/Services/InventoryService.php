<?php

// "Otak" utama (Service Layer) di balik semua logika bisnis Inventaris.
// Bertugas memproses pencarian data, eksekusi penyimpanan barang, penggabungan stok, transaksi peminjaman, hingga manajemen file.
namespace App\Services;

use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use App\Notifications\ApproachingStockNotification;
use App\Notifications\ItemReturnedNotification;
use App\Notifications\LowStockNotification;
use App\Notifications\StockRequestNotification;
use App\Traits\ActivityLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InventoryService
{
    use ActivityLogger;

    protected $imageOptimizer;

    protected $qrCodeService;

    public function __construct(ImageOptimizationService $imageOptimizer, QrCodeService $qrCodeService)
    {
        $this->imageOptimizer = $imageOptimizer;
        $this->qrCodeService = $qrCodeService;
    }

    // Mengambil daftar sparepart dari database berdasarkan filter pencarian dan membaginya per halaman
    public function getFilteredSpareparts(array $filters, int $perPage = 10)
    {
        // Membuka query awal beserta relasi datanya agar tidak lambat saat ditampilkan (Eager Loading)
        $query = Sparepart::with(['brand', 'category', 'location']);

        // Jika filter tempat sampah aktif, hanya ambil data yang sudah terhapus sementara (soft delete)
        if (($filters['trash'] ?? '') === 'true') {
            $query->onlyTrashed();
        }

        // Jika user mengetik sesuatu di kolom pencarian, cari di kolom nama, part number, atau nama brand
        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['search'].'%')
                    ->orWhere('part_number', 'like', '%'.$filters['search'].'%')
                    ->orWhereHas('brand', function ($bq) use ($filters) {
                        $bq->where('name', 'like', '%'.$filters['search'].'%');
                    });
            });
        }

        // Menerapkan filter dropdown jika user memilih opsi spesifik (kategori, brand, lokasi, tipe, dsb)
        $this->applyRelationFilter($query, 'category', $filters['category'] ?? null, __('messages.all_categories'));
        $this->applyRelationFilter($query, 'brand', $filters['brand'] ?? null, __('messages.all_brands'));
        $this->applyRelationFilter($query, 'location', $filters['location'] ?? null, __('messages.all_locations'));
        $this->applyExactFilter($query, 'color', $filters['color'] ?? null, __('messages.all_colors'));
        $this->applyExactFilter($query, 'type', $filters['type'] ?? null, __('messages.all_types'));
        $this->applyExactFilter($query, 'condition', $filters['condition'] ?? null, __('messages.all_conditions'));

        // Filter tab khusus yang ada di dashboard (seperti tab 'stok menipis' atau 'barang bermasalah')
        if (($filters['filter'] ?? '') === 'low_stock') {
            // Hanya mencari barang yang nilai stok-nya sudah menyentuh atau kurang dari minimum_stock
            $query->lowStock();
        } elseif (($filters['filter'] ?? '') === 'overdue') {
            // Mencari barang yang sedang dipinjam tapi sudah melewati batas waktu kembalinya
            $query->whereHas('borrowings', function ($q) {
                $q->overdue();
            });
        } elseif (($filters['filter'] ?? '') === 'borrowed') {
            // Mencari barang apa saja yang saat ini statusnya sedang dipinjam
            $query->whereHas('borrowings', function ($q) {
                $q->active();
            });
        } elseif (($filters['filter'] ?? '') === 'no_price') {
            // Mencari barang yang belum memiliki harga atau harganya 0
            $query->noPrice();
        } elseif (($filters['filter'] ?? '') === 'problematic') {
            if (auth()->check() && auth()->user()->role === \App\Enums\UserRole::OPERATOR) {
                // Operator tidak boleh melihat barang bermasalah, paksa query kosong
                $query->whereRaw('1 = 0');
            } else {
                // Menampilkan barang dengan kondisi Rusak/Hilang
                $query->problematic()
                    ->with(['stockLogs' => fn ($q) => $q->latest()]);
            }
        }

        // Terapkan pengurutan data sesuai opsi sort (A-Z, Termurah, Terbaru, dll)
        $this->applySorting($query, $filters['sort'] ?? null);

        // Kembalikan hasilnya dalam bentuk pagination (halaman) dan menempelkan parameter filter pada URL-nya
        return $query->paginate($perPage)->appends($filters);
    }

    // Membuat barang (sparepart) baru ke database. Jika ketemu barang identik, stoknya digabung (merge).
    public function createSparepart(array $data)
    {
        // Membuat kunci gembok (lock) unik berdasarkan identitas detail barang
        // Ini berguna untuk menghindari error ketika user ngeklik tombol 'Simpan' dua kali (Double Submit)
        $lockKey = 'create_sparepart_'.md5(
            ($data['part_number'] ?? '').
            ($data['name'] ?? '').
            ($data['brand_id'] ?? '').
            ($data['category_id'] ?? '').
            ($data['location_id'] ?? '').
            ($data['condition'] ?? '').
            ($data['type'] ?? '')
        );

        // Kunci proses pembuatan barang ini maksimal selama 5 detik
        $lock = Cache::lock($lockKey, 5); 

        // Jika lock sedang dipakai (artinya ada request yg sedang diproses), tolak request yang baru masuk
        if (! $lock->get()) {
            return ['status' => 'error_zero_stock', 'message' => __('Sistem mendeteksi proses ganda. Silakan tunggu sebentar.'), 'data' => null];
        }

        try {
            $newImageUploaded = null;
            // Memulai transaksi DB agar jika error di tengah jalan, semua dibatalkan (Rollback) agar data tidak berantakan
            return DB::transaction(function () use (&$data, &$newImageUploaded) {
                
                // Mengecek ke database apakah ada barang yang atributnya sama persis
                $existingItem = $this->findExactDuplicate($data);

                if ($existingItem) {
                    // Jika ketemu barang identik, jangan buat data baru. Tambahkan saja stoknya!
                    if ($data['stock'] > 0) {
                        $existingItem->stock += $data['stock'];
                        $existingItem->save();

                        $message = __('messages.stock_merged', [
                            'name' => $existingItem->name,
                            'part_number' => $existingItem->part_number,
                        ]);

                        // Catat log persediaan bahwa ada stok masuk hasil dari penggabungan
                        StockLog::create([
                            'sparepart_id' => $existingItem->id,
                            'user_id' => auth()->id(),
                            'type' => 'masuk',
                            'quantity' => $data['stock'],
                            'reason' => __('messages.log_stock_added_duplicate'),
                            'status' => 'approved',
                            'approved_by' => auth()->id(),
                        ]);

                        // Catat jejak aktivitas user
                        $this->logActivity('Stok Diupdate', $message, [
                            'stock' => [
                                'old' => $existingItem->stock - $data['stock'],
                                'new' => $existingItem->stock,
                            ],
                        ]);
                        
                        // Hapus cache memori lama & tembak websocket agar layar device lain ter-update otomatis
                        $this->clearCache();
                        $this->broadcastUpdate($existingItem, 'updated');

                        return ['status' => 'merged', 'message' => $message, 'data' => $existingItem];
                    } else {
                        // Jika mau menggabung barang tapi input stoknya 0, kita tolak prosesnya
                        $message = __('messages.stock_zero_duplicate', [
                            'name' => $existingItem->name,
                            'part_number' => $existingItem->part_number,
                        ]);

                        return ['status' => 'error_zero_stock', 'message' => $message, 'data' => $existingItem];
                    }
                }

                // --- Jika barang benar-benar baru, lanjut buat entri datanya ---

                // Jika user mengunggah foto, optimalkan ukurannya dulu sebelum disimpan
                if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $data['image'] = $this->imageOptimizer->optimizeAndSave($data['image'], 'spareparts');
                    $newImageUploaded = $data['image'];
                } elseif (! empty($data['existing_image'])) {
                    // Jika memanfaatkan foto dari riwayat sebelumnya, gandakan file fisiknya
                    $existingPath = $data['existing_image'];
                    if (Storage::disk('public')->exists($existingPath)) {
                        $extension = pathinfo($existingPath, PATHINFO_EXTENSION);
                        $newPath = 'spareparts/'.Str::random(40).'.'.$extension;
                        Storage::disk('public')->copy($existingPath, $newPath);
                        $data['image'] = $newPath;
                        $newImageUploaded = $data['image'];
                    }
                }

                // Simpan barang baru ke database
                $sparepart = Sparepart::create($data);
                
                // Panggil ulang dari DB agar mendapatkan UUID hasil generate
                $sparepart->refresh(); 
                
                // Secara otomatis membuat gambar label QR Code untuk ditempel di barang
                $this->qrCodeService->generate($sparepart);

                // Jika saat dibuat langsung ada isi stoknya, catat log stok awal
                if ($sparepart->stock > 0) {
                    StockLog::create([
                        'sparepart_id' => $sparepart->id,
                        'user_id' => auth()->id(),
                        'type' => 'masuk',
                        'quantity' => $sparepart->stock,
                        'reason' => __('messages.log_stock_initial'),
                        'status' => 'approved',
                        'approved_by' => auth()->id(),
                    ]);
                }

                $message = __('messages.item_created', [
                    'name' => $sparepart->name,
                    'part_number' => $sparepart->part_number,
                ]);

                // Simpan aktivitas log, bersihkan cache aplikasi, & broadcast event ke frontend
                $this->logActivity('Sparepart Dibuat', $message);
                $this->clearCache();
                $this->broadcastUpdate($sparepart, 'created');

                return ['status' => 'created', 'message' => $message, 'data' => $sparepart];
            });
        } catch (\Exception $e) {
            // Hapus file foto yang terlanjur terupload jika proses gagal
            if ($newImageUploaded && Storage::disk('public')->exists($newImageUploaded)) {
                Storage::disk('public')->delete($newImageUploaded);
            }
            throw $e;
        } finally {
            // Setelah semua aksi DB selesai atau pun error, cabut perlindungan lock-nya
            $lock->release();
        }
    }

    // Fitur Pembaruan: Menyimpan perubahan data barang dan otomatis mencetak ulang stiker QR jika nama atau nomor serinya berubah.
    public function updateSparepart(Sparepart $sparepart, array $data)
    {
        $oldImage = null;
        $newImageUploaded = null;
        $oldQr = null;

        try {
            $result = DB::transaction(function () use ($sparepart, $data, &$oldImage, &$newImageUploaded, &$oldQr) {
                if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                    $oldImage = $sparepart->image;
                    $data['image'] = $this->imageOptimizer->optimizeAndSave($data['image'], 'spareparts');
                    $newImageUploaded = $data['image'];
                } elseif (array_key_exists('existing_image', $data) && empty($data['existing_image'])) {
                    // User menghapus foto (klik ikon X) tanpa mengunggah foto baru
                    $oldImage = $sparepart->image;
                    $data['image'] = null; // Set field image di database menjadi null
                }

                // Hapus existing_image dari array data agar tidak di-fill ke model
                unset($data['existing_image']);

                $sparepart->fill($data);

                // Regenerasi QR hanya jika part_number berubah untuk efisiensi
                if ($sparepart->wasChanged('part_number') || ! $sparepart->qr_code_path) {
                    if ($sparepart->getOriginal('qr_code_path')) {
                        $oldQr = $sparepart->getOriginal('qr_code_path');
                    }
                    $this->qrCodeService->generate($sparepart);
                }

            $changes = [];
            $stockDiff = 0;
            if ($sparepart->isDirty()) {
                foreach ($sparepart->getDirty() as $key => $value) {
                    $original = $sparepart->getOriginal($key);
                    $changes[$key] = ['old' => $original, 'new' => $value];
                    if ($key === 'stock') {
                        $stockDiff = $value - $original;
                    }
                }
            }

            $sparepart->save();

            if ($stockDiff !== 0) {
                StockLog::create([
                    'sparepart_id' => $sparepart->id,
                    'user_id' => auth()->id(),
                    'type' => $stockDiff > 0 ? 'masuk' : 'keluar',
                    'quantity' => abs($stockDiff),
                    'reason' => 'Penyesuaian stok manual (Update Data Barang)',
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                ]);
            }

            $this->logActivity('Sparepart Diperbarui', __('messages.log_item_updated', ['name' => $sparepart->name, 'part_number' => $sparepart->part_number]), $changes);
            $this->clearCache();
            $this->broadcastUpdate($sparepart, 'updated');

            // Notifikasi stok rendah — hanya kirim jika nilai stok benar-benar berubah di save ini
            if ($sparepart->wasChanged('stock') && strtolower($sparepart->condition) === 'baik') {
                if ($sparepart->minimum_stock > 0 && $sparepart->stock <= $sparepart->minimum_stock) {
                    $admins = User::whereIn('role', [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])->get();
                    Notification::send($admins, new LowStockNotification($sparepart));

                    $severity = $sparepart->stock === 0 ? 'depleted' : 'critical';
                    try {
                        broadcast(new \App\Events\StockCriticalEvent($sparepart, $severity));
                    } catch (\Throwable $e) {
                    }

                    } elseif ($sparepart->minimum_stock > 0 && $sparepart->stock <= ($sparepart->minimum_stock + 5)) {
                        // Notifikasi approaching: stok menuju minimum (selisih <= 5 dari minimum)
                        $admins = User::whereIn('role', [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])->get();
                        Notification::send($admins, new ApproachingStockNotification($sparepart));
                    }
                }

                return ['status' => 'success', 'message' => __('messages.item_updated'), 'data' => $sparepart];
            });

            // Hapus file fisik lama JIKA transaksi DB sukses sepenuhnya
            if ($oldImage && Storage::disk('public')->exists($oldImage)) {
                Storage::disk('public')->delete($oldImage);
            }
            if ($oldQr && Storage::disk('public')->exists($oldQr)) {
                Storage::disk('public')->delete($oldQr);
            }

            return $result;

        } catch (\Exception $e) {
            // Hapus file foto yang terlanjur terupload JIKA transaksi gagal di tengah jalan
            if ($newImageUploaded && Storage::disk('public')->exists($newImageUploaded)) {
                Storage::disk('public')->delete($newImageUploaded);
            }
            throw $e;
        }
    }

    // Fitur Hapus Sementara: Membuang barang ke tong sampah (Soft Delete) agar bisa dikembalikan lagi jika salah klik.
    public function deleteSparepart(Sparepart $sparepart)
    {
        return DB::transaction(function () use ($sparepart) {
            // Validasi: item tidak boleh dihapus jika masih ada peminjaman aktif
            if ($sparepart->borrowings()->whereIn('status', ['borrowed', 'overdue'])->exists()) {
                return ['status' => 'error', 'message' => __('messages.cannot_delete_borrowed_item')];
            }

            $this->logActivity('Sparepart Dihapus', __('messages.log_item_deleted_soft', ['name' => $sparepart->name, 'part_number' => $sparepart->part_number]));
            $sparepart->delete();
            $this->clearCache();
            $this->broadcastUpdate($sparepart, 'deleted');

            return ['status' => 'deleted', 'message' => __('messages.item_deleted')];
        });
    }

    // Fitur Pemulihan: Mengembalikan barang dari dalam tong sampah agar bisa digunakan lagi di gudang.
    public function restoreSparepart($id)
    {
        return DB::transaction(function () use ($id) {
            $sparepart = Sparepart::onlyTrashed()->findOrFail($id);
            $sparepart->restore();

            $this->logActivity('Sparepart Dipulihkan', __('messages.log_item_restored', ['name' => $sparepart->name, 'part_number' => $sparepart->part_number]));
            $this->clearCache();

            return ['status' => 'restored', 'message' => __('messages.item_restored')];
        });
    }

    // Fitur Hapus Permanen: Menghancurkan data barang seutuhnya dari database beserta seluruh jejak fotonya.
    public function forceDeleteSparepart($id)
    {
        return DB::transaction(function () use ($id) {
            $sparepart = Sparepart::onlyTrashed()->findOrFail($id);

            if ($sparepart->borrowings()->whereIn('status', ['borrowed', 'overdue'])->exists()) {
                return ['status' => 'error', 'message' => __('messages.cannot_delete_borrowed_item')];
            }

            if ($sparepart->qr_code_path && Storage::disk('public')->exists($sparepart->qr_code_path)) {
                Storage::disk('public')->delete($sparepart->qr_code_path);
            }
            if ($sparepart->image && Storage::disk('public')->exists($sparepart->image)) {
                Storage::disk('public')->delete($sparepart->image);
            }

            $sparepart->forceDelete();

            $this->logActivity('Sparepart Dihapus Permanen', __('messages.log_item_deleted_force', ['name' => $sparepart->name, 'part_number' => $sparepart->part_number]));
            $this->clearCache();

            return ['status' => 'force_deleted', 'message' => __('messages.item_force_deleted')];
        });
    }

    // Fitur Bersih-Bersih: Mengosongkan seluruh isi tong sampah dalam satu kali klik.
    public function forceDeleteAllSpareparts()
    {
        return DB::transaction(function () {
            $spareparts = Sparepart::onlyTrashed()->get();

            if ($spareparts->isEmpty()) {
                return ['status' => 'empty', 'message' => __('messages.trash_empty')];
            }

            $names = [];
            /** @var \App\Models\Sparepart $sparepart */
            foreach ($spareparts as $sparepart) {
                if ($sparepart->qr_code_path && Storage::disk('public')->exists($sparepart->qr_code_path)) {
                    Storage::disk('public')->delete($sparepart->qr_code_path);
                }
                if ($sparepart->image && Storage::disk('public')->exists($sparepart->image)) {
                    Storage::disk('public')->delete($sparepart->image);
                }
                $names[] = $sparepart->part_number . ' - ' . $sparepart->name;
                $sparepart->forceDelete();
            }

            $namesList = implode(', ', $names);

            $this->logActivity('Tong Sampah Dikosongkan', __('messages.log_trash_cleared', ['count' => $spareparts->count()]), [
                'items' => ['old' => $namesList, 'new' => '-']
            ]);
            $this->clearCache();

            return ['status' => 'all_deleted', 'message' => __('messages.trash_cleared')];
        });
    }

    // Fitur Pulihkan Massal: Mengembalikan banyak barang dari tong sampah sekaligus tanpa perlu klik satu per satu.
    public function bulkRestore(array $ids)
    {
        return DB::transaction(function () use ($ids) {
            $count = Sparepart::onlyTrashed()->whereIn('id', $ids)->count();
            if ($count === 0) {
                return ['status' => 'empty', 'message' => __('messages.no_item_selected')];
            }

            $spareparts = Sparepart::onlyTrashed()->whereIn('id', $ids)->get();
            $names = [];
            foreach ($spareparts as $sparepart) {
                $names[] = $sparepart->part_number . ' - ' . $sparepart->name;
                $sparepart->restore();
            }

            $namesList = implode(', ', $names);

            $this->logActivity('Pemulihan Massal', __('messages.log_bulk_restored', ['count' => $count]), [
                'items' => ['old' => '-', 'new' => $namesList]
            ]);
            $this->clearCache();

            return ['status' => 'success', 'message' => __('messages.bulk_restored', ['count' => $count])];
        });
    }

    // Fitur Hapus Permanen Massal: Menghancurkan banyak data barang sekaligus secara permanen (Tidak bisa dikembalikan).
    public function bulkForceDelete(array $ids)
    {
        return DB::transaction(function () use ($ids) {
            $spareparts = Sparepart::onlyTrashed()->whereIn('id', $ids)->get();
            if ($spareparts->isEmpty()) {
                return ['status' => 'empty', 'message' => __('messages.no_item_selected')];
            }

            $names = [];
            /** @var \App\Models\Sparepart $sparepart */
            foreach ($spareparts as $sparepart) {
                if ($sparepart->qr_code_path && Storage::disk('public')->exists($sparepart->qr_code_path)) {
                    Storage::disk('public')->delete($sparepart->qr_code_path);
                }
                if ($sparepart->image && Storage::disk('public')->exists($sparepart->image)) {
                    Storage::disk('public')->delete($sparepart->image);
                }
                $names[] = $sparepart->part_number . ' - ' . $sparepart->name;
                $sparepart->forceDelete();
            }

            $namesList = implode(', ', $names);

            $this->logActivity('Hapus Permanen Massal', __('messages.log_bulk_deleted_force', ['count' => $spareparts->count()]), [
                'items' => ['old' => $namesList, 'new' => '-']
            ]);
            $this->clearCache();

            return ['status' => 'success', 'message' => __('messages.bulk_force_deleted', ['count' => $spareparts->count()])];
        });
    }

    // Fitur Pengumpul Pilihan: Menyiapkan daftar opsi (Kategori, Merek, Lokasi) untuk menu dropdown.
    // Disimpan di memori sementara (Cache) agar form tambah barang bisa dimuat secepat kilat.
    public function getDropdownOptions()
    {
        return [
            // Data {id, name} untuk form create/edit (submit ID)
            'categories' => Cache::remember('inventory_categories', 3600, function () {
                return \App\Models\Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);
            }),
            'brands' => Cache::remember('inventory_brands', 3600, function () {
                return \App\Models\Brand::where('is_active', true)->orderBy('name')->get(['id', 'name']);
            }),
            'locations' => Cache::remember('inventory_locations', 3600, function () {
                return \App\Models\Location::where('is_active', true)->orderBy('name')->get(['id', 'name']);
            }),
            // Data nama saja untuk filter dropdown di halaman index
            'categoryOptions' => Cache::remember('inventory_category_options', 3600, function () {
                return \App\Models\Category::where('is_active', true)->orderBy('name')->pluck('name');
            }),
            'brandOptions' => Cache::remember('inventory_brand_options', 3600, function () {
                return \App\Models\Brand::where('is_active', true)->orderBy('name')->pluck('name');
            }),
            'locationOptions' => Cache::remember('inventory_location_options', 3600, function () {
                return \App\Models\Location::where('is_active', true)->orderBy('name')->pluck('name');
            }),
            'colors' => Cache::remember('inventory_colors', 3600, fn () => Sparepart::whereNotNull('color')->select('color')->distinct()->pluck('color')),
            'units' => Cache::remember('inventory_units', 3600, fn () => Sparepart::whereNotNull('unit')->select('unit')->distinct()->pluck('unit')),
            'names' => Cache::remember('inventory_names', 3600, fn () => Sparepart::select('name')->distinct()->pluck('name')),
            'partNumbers' => Cache::remember('inventory_part_numbers', 3600, fn () => Sparepart::select('part_number')->distinct()->pluck('part_number')),
            'conditions' => Cache::remember('inventory_conditions', 3600, fn () => Sparepart::whereNotNull('condition')->select('condition')->distinct()->pluck('condition')),
        ];
    }

    // Fitur Pembersih Memori (Cache): Menghapus data ingatan lama agar aplikasi mengambil data terbaru dari database.
    public function clearCache()
    {
        Cache::forget('inventory_categories');
        Cache::forget('inventory_brands');
        Cache::forget('inventory_locations');
        Cache::forget('inventory_category_options');
        Cache::forget('inventory_brand_options');
        Cache::forget('inventory_location_options');
        Cache::forget('inventory_colors');
        Cache::forget('inventory_units');
        Cache::forget('inventory_names');
        Cache::forget('inventory_part_numbers');
        Cache::forget('inventory_conditions');
        Cache::forget('dashboard_available_years');

        // Update timestamp global untuk memicu refresh data di Dashboard
        Cache::forever('inventory_last_updated', now()->timestamp);
    }

    private function applyExactFilter(Builder $query, string $column, ?string $value, string $ignoreValue)
    {
        if ($value && $value !== $ignoreValue) {
            $query->where($column, $value);
        }
    }

    // Filter Cerdas: Membantu mencari barang berdasarkan data yang terhubung (Contoh: Mencari nama Kategorinya, bukan angka ID-nya).
    private function applyRelationFilter(Builder $query, string $relation, ?string $value, string $ignoreValue)
    {
        if ($value && $value !== $ignoreValue) {
            $query->whereHas($relation, function ($q) use ($value) {
                $q->where('name', $value);
            });
        }
    }

    private function applySorting(Builder $query, ?string $sort)
    {
        if (! $sort) {
            $query->latest();

            return;
        }

        switch ($sort) {
            case 'name_asc': $query->orderBy('name', 'asc');
                break;
            case 'name_desc': $query->orderBy('name', 'desc');
                break;
            case 'stock_asc': $query->orderBy('stock', 'asc');
                break;
            case 'stock_desc': $query->orderBy('stock', 'desc');
                break;
            case 'price_asc': $query->orderBy('price', 'asc');
                break;
            case 'price_desc': $query->orderBy('price', 'desc');
                break;
            case 'oldest': $query->oldest();
                break;
            case 'newest': default: $query->latest();
                break;
        }
    }

    // Sistem Detektif: Mengecek apakah barang yang sedang diedit ini nantinya akan sama persis 100% dengan barang lain di gudang.
    public function checkUpdateDuplicate(Sparepart $currentItem, array $data)
    {
        $checkData = array_merge($currentItem->toArray(), $data);

        $query = Sparepart::where('id', '!=', $currentItem->id)
            ->where('part_number', $checkData['part_number'])
            ->where('name', $checkData['name'])
            ->where('brand_id', $checkData['brand_id'])
            ->where('category_id', $checkData['category_id'])
            ->where('location_id', $checkData['location_id'])
            ->where('condition', $checkData['condition'])
            ->where('type', $checkData['type']);

        foreach (['color', 'price', 'unit'] as $field) {
            if (isset($checkData[$field])) {
                $query->where($field, $checkData[$field]);
            } else {
                $query->whereNull($field);
            }
        }

        return $query->first();
    }

    // Fitur Penggabungan (Merge): Menyatukan dua barang yang identik kembar menjadi satu kesatuan agar data tidak berceceran ganda.
    public function mergeSpareparts(Sparepart $source, Sparepart $target)
    {
        return DB::transaction(function () use ($source, $target) {
            if ($source->borrowings()->whereIn('status', ['borrowed', 'overdue'])->exists()) {
                return ['status' => 'error', 'message' => __('messages.cannot_merge_borrowed_item')];
            }

            $stockToAdd = $source->stock;
            $target->stock += $stockToAdd;
            $target->save();

            // Alihkan seluruh riwayat peminjaman dan log stok ke item tujuan
            $source->borrowings()->update(['sparepart_id' => $target->id]);
            $source->stockLogs()->update(['sparepart_id' => $target->id]);

            if ($stockToAdd > 0) {
                StockLog::create([
                    'sparepart_id' => $target->id,
                    'user_id' => auth()->id(),
                    'type' => 'masuk',
                    'quantity' => $stockToAdd,
                    'reason' => __('messages.log_stock_merged_from', ['source_pn' => $source->part_number]),
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                ]);
            }

            $source->delete();

            $message = __('messages.items_merged', [
                'source_name' => $source->name, 'source_pn' => $source->part_number,
                'target_name' => $target->name, 'target_pn' => $target->part_number,
                'stock' => $stockToAdd,
            ]);

            $this->logActivity('Penggabungan Sparepart', $message, [
                'stock' => [
                    'old' => $target->stock - $stockToAdd,
                    'new' => $target->stock,
                ],
            ]);
            $this->clearCache();
            $this->broadcastUpdate($target, 'updated');

            return ['status' => 'merged', 'message' => $message, 'data' => $target];
        });
    }

    // Fitur Pendeteksi Kembaran: Memastikan tidak ada barang dengan spesifikasi yang persis sama 100% dibuat secara tidak sengaja.
    private function findExactDuplicate(array $data)
    {
        $existingItemQuery = Sparepart::where('part_number', $data['part_number'])
            ->where('name', $data['name'])
            ->where('brand_id', $data['brand_id'])
            ->where('category_id', $data['category_id'])
            ->where('location_id', $data['location_id'])
            ->where('condition', $data['condition'])
            ->where('type', $data['type']);

        foreach (['color', 'price', 'unit'] as $field) {
            if (isset($data[$field])) {
                $existingItemQuery->where($field, $data[$field]);
            } else {
                $existingItemQuery->whereNull($field);
            }
        }

        // Lock for update untuk mencegah inkonsistensi saat concurrent request
        return $existingItemQuery->lockForUpdate()->first();
    }

    // Fitur Peminjaman: Memproses keluarnya barang, mencatat siapa yang pinjam, dan otomatis memotong sisa stok di database.
    public function createBorrowing(Sparepart $sparepart, array $data)
    {
        return DB::transaction(function () use ($sparepart, $data) {
            $sparepart = Sparepart::where('id', $sparepart->id)->lockForUpdate()->first();
            if (!$sparepart) {
                throw new \Exception('Barang tidak ditemukan atau sudah dihapus.');
            }

            if ($sparepart->stock < $data['quantity']) {
                throw new \Exception(__('messages.insufficient_stock'));
            }

            $borrowing = Borrowing::create([
                'sparepart_id' => $sparepart->id,
                'user_id' => $data['user_id'] ?? auth()->id(),
                'borrower_name' => $data['borrower_name'] ?? (auth()->user() ? auth()->user()->name : 'System'),
                'quantity' => $data['quantity'],
                'borrowed_at' => now(),
                'expected_return_at' => $data['expected_return_at'],
                'notes' => $data['notes'] ?? null,
                'status' => 'borrowed',
            ]);

            $sparepart->decrement('stock', $data['quantity']);

            StockLog::create([
                'sparepart_id' => $sparepart->id,
                'user_id' => auth()->id(),
                'type' => 'keluar',
                'quantity' => $data['quantity'],
                'reason' => __('messages.log_borrowing', ['user' => auth()->user()->name]),
                'status' => 'approved',
                'approved_by' => auth()->id(),
            ]);

            $sparepart->refresh();
            if (strtolower($sparepart->condition) === 'baik') {
                if ($sparepart->minimum_stock > 0 && $sparepart->stock <= $sparepart->minimum_stock) {
                    $admins = User::whereIn('role', [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])->get();
                    Notification::send($admins, new LowStockNotification($sparepart));

                    $severity = $sparepart->stock === 0 ? 'depleted' : 'critical';
                    try {
                        broadcast(new \App\Events\StockCriticalEvent($sparepart, $severity));
                    } catch (\Throwable $e) {
                    }

                } elseif ($sparepart->minimum_stock > 0 && $sparepart->stock <= ($sparepart->minimum_stock + 5)) {
                    $admins = User::whereIn('role', [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])->get();
                    Notification::send($admins, new ApproachingStockNotification($sparepart));
                }
            }

            $this->logActivity('Peminjaman Barang', "Meminjam {$data['quantity']} {$sparepart->unit} '{$sparepart->name}'.", [
                'stock' => [
                    'old' => $sparepart->stock + $data['quantity'],
                    'new' => $sparepart->stock,
                ],
            ]);
            $this->clearCache();
            $this->broadcastUpdate($sparepart, 'borrowing', __('messages.realtime_borrowed', ['user' => auth()->user()->name, 'qty' => $data['quantity'], 'name' => $sparepart->name]));

            return ['status' => 'created', 'borrowing' => $borrowing];
        });
    }

    // Fitur Pengembalian: Mencatat barang yang dikembalikan dan memulihkan stoknya.
    // Jika barang rusak/hilang, sistem akan memisahkan barang tersebut ke dalam entitas tersendiri secara cerdas.
    public function returnBorrowing(Borrowing $borrowing, array $data, array $photos = [])
    {
        return DB::transaction(function () use ($borrowing, $data, $photos) {
            // Pessimistic Locking untuk mencegah race condition / duplikasi stok
            $borrowing = Borrowing::where('id', $borrowing->id)->lockForUpdate()->first();
            if (!$borrowing) {
                throw new \Exception('Transaksi peminjaman tidak ditemukan.');
            }

            $qty = $data['return_quantity'];

            // Validasi ulang di dalam lock
            if ($qty > $borrowing->remaining_quantity) {
                throw new \Exception('Jumlah pengembalian melebihi sisa pinjaman (indikasi concurrent request).');
            }

            $condition = $data['return_condition'];
            $originalSparepart = $borrowing->sparepart;

            $translatedCondition = null;

            $borrowing->returns()->create([
                'return_date' => now(),
                'quantity' => $qty,
                'condition' => $condition,
                'notes' => $data['return_notes'] ?? null,
                'photos' => $photos,
            ]);

            $newTotalReturned = $borrowing->returns()->sum('quantity');
            if ($newTotalReturned >= $borrowing->quantity) {
                $borrowing->update([
                    'status' => 'returned',
                    'returned_at' => now(),
                ]);
            }

            if ($condition === 'good') {
                $originalSparepart->increment('stock', $qty);

                StockLog::create([
                    'sparepart_id' => $originalSparepart->id,
                    'user_id' => auth()->id(),
                    'type' => 'masuk',
                    'quantity' => $qty,
                    'reason' => __('messages.log_return_good', ['user' => $borrowing->borrower_name]),
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                ]);

                $this->logActivity('Pengembalian Barang (Baik)', "Mengembalikan {$qty} unit '{$originalSparepart->name}' dalam kondisi Baik.", [
                    'stock' => [
                        'old' => $originalSparepart->stock - $qty,
                        'new' => $originalSparepart->stock,
                    ],
                ]);

            } else {
                // Alur penanganan aset yang dikembalikan dalam kondisi Rusak/Hilang
                $translatedCondition = ($condition === 'bad') ? 'Rusak' : 'Hilang';
                $targetItem = Sparepart::where('part_number', $originalSparepart->part_number)
                    ->where('condition', $translatedCondition)
                    ->first();

                if ($targetItem) {
                    $targetItem->increment('stock', $qty);
                } else {
                    $targetItem = $originalSparepart->replicate();
                    $targetItem->condition = $translatedCondition;
                    $targetItem->stock = $qty;
                    $targetItem->save();
                    $this->qrCodeService->generate($targetItem);
                }

                StockLog::create([
                    'sparepart_id' => $targetItem->id,
                    'user_id' => auth()->id(),
                    'type' => 'masuk',
                    'quantity' => $qty,
                    'reason' => 'Pengembalian barang dalam kondisi ' . $translatedCondition . ' oleh ' . ($borrowing->borrower_name ?? 'Peminjam'),
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                ]);

                $this->logActivity('Pengembalian Barang ('.$translatedCondition.')', "Mengembalikan {$qty} unit '{$originalSparepart->name}' dalam kondisi {$translatedCondition}.", [
                    'stock' => [
                        'old' => $targetItem->stock - $qty,
                        'new' => $targetItem->stock,
                    ],
                ]);
            }

            $this->clearCache();
            $this->broadcastUpdate($originalSparepart, 'returned', __('messages.realtime_returned', ['user' => auth()->user()->name, 'qty' => $qty, 'name' => $originalSparepart->name]));

            // Kirim notifikasi ke Admin & Superadmin
            $admins = User::whereIn('role', [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])->get();
            Notification::send($admins, new ItemReturnedNotification($borrowing, $qty, $translatedCondition ?? 'Baik'));

            return ['status' => 'success'];
        });
    }

    // Fitur Validasi Atasan (Approval Flow): Memproses izin (Terima/Tolak) untuk setiap pergerakan stok yang diajukan oleh staf.
    public function approveStockRequest(StockLog $stockLog, string $status, ?string $rejectionReason = null)
    {
        return DB::transaction(function () use ($stockLog, $status, $rejectionReason) {
            // Pessimistic Locking untuk mencegah dua admin menyetujui log yang sama serentak
            $lockedLog = clone $stockLog;
            if ($lockedLog->exists) {
                $lockedLog = StockLog::where('id', $stockLog->id)->lockForUpdate()->first();
            }

            if ($lockedLog->status !== 'pending') {
                throw new \Exception('Pengajuan ini sudah diproses sebelumnya.');
            }

            $oldStock = null;
            if ($status === 'approved') {
                $sparepart = Sparepart::where('id', $stockLog->sparepart_id)->lockForUpdate()->first();
                if (!$sparepart) {
                    throw new \Exception('Barang tidak ditemukan atau sudah dihapus. Pengajuan tidak dapat disetujui.');
                }
                
                $oldStock = $sparepart->stock;

                if ($stockLog->type === 'masuk') {
                    $sparepart->stock += $stockLog->quantity;
                } else { // keluar
                    if ($sparepart->stock < $stockLog->quantity) {
                        throw new \Exception('Stok tidak mencukupi untuk permintaan ini.');
                    }
                    $sparepart->stock -= $stockLog->quantity;
                }
                $sparepart->save();

                $this->clearCache();
                $actionType = $stockLog->type === 'masuk' ? 'success' : 'warning';
                $actionText = $stockLog->type === 'masuk' ? 'menambah stok' : 'mengurangi stok';
                $adminName = auth()->user() ? auth()->user()->name : 'System';
                $customMessage = "{$adminName} menyetujui {$actionText} sebanyak {$stockLog->quantity} {$sparepart->unit} pada barang: {$sparepart->name}";
                $this->broadcastUpdate($sparepart, $actionType, $customMessage);

                if ($sparepart->minimum_stock > 0 && $sparepart->stock <= $sparepart->minimum_stock) {
                    $admins = User::whereIn('role', [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])->get();
                    Notification::send($admins, new LowStockNotification($sparepart));

                    $severity = $sparepart->stock === 0 ? 'depleted' : 'critical';
                    try {
                        broadcast(new \App\Events\StockCriticalEvent($sparepart, $severity));
                    } catch (\Throwable $e) {
                    }
                } elseif ($sparepart->minimum_stock > 0 && $sparepart->stock <= ($sparepart->minimum_stock + 5)) {
                    $admins = User::whereIn('role', [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])->get();
                    Notification::send($admins, new ApproachingStockNotification($sparepart));
                }
            }

            $updateData = [
                'status' => $status,
                'approved_by' => auth()->id(),
            ];
            if ($status === 'rejected' && $rejectionReason) {
                $updateData['rejection_reason'] = $rejectionReason;
            }
            $lockedLog->update($updateData);

            $statusText = $status === 'approved' ? 'disetujui' : 'ditolak';
            $sparepartName = $lockedLog->sparepart ? $lockedLog->sparepart->name : 'Barang Terhapus';
            $description = "Pengajuan stok {$lockedLog->type} untuk '{$sparepartName}' sejumlah {$lockedLog->quantity} telah {$statusText}.";
            if ($status === 'rejected' && $rejectionReason) {
                $description .= " Alasan: {$rejectionReason}";
            }

            $properties = [];
            if ($status === 'approved' && isset($sparepart)) {
                $properties['stock'] = [
                    'old' => $oldStock,
                    'new' => $sparepart->stock,
                ];
            }

            $this->logActivity(
                'Persetujuan Stok',
                $description,
                $properties
            );

            // Broadcast real-time stock approval processing (remove from list)
            try {
                broadcast(new \App\Events\StockApprovalUpdatedEvent($lockedLog->fresh(), 'processed'))->toOthers();
            } catch (\Throwable $e) {
            }

            // Notifikasi balik ke pemohon (Operator/Admin) mengenai hasil approval
            $requester = $lockedLog->user;
            if ($requester) {
                $message = __('ui.notification_stock_request_body', [
                    'type' => $lockedLog->type,
                    'name' => $lockedLog->sparepart->name,
                    'status' => $statusText,
                ]);
                Notification::send($requester, new StockRequestNotification($lockedLog, $message));
            }

            return ['status' => 'success'];
        });
    }

    // Fitur Real-Time (Websocket): Mengumumkan ke semua layar pengguna lain bahwa ada perubahan data.
    // Tujuannya agar layar mereka ikut ter-update otomatis secara "Gaib" tanpa perlu menekan tombol refresh.
    public function broadcastUpdate(Sparepart $sparepart, string $action, ?string $customMessage = null)
    {
        try {
            broadcast(new \App\Events\InventoryUpdatedEvent(
                $sparepart->fresh(),
                $action,
                auth()->user()?->name ?? 'System',
                $customMessage
            ))->toOthers();
        } catch (\Throwable $e) {
        }
    }
}
