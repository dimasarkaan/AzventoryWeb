<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use App\Notifications\ApproachingStockNotification;
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

    /**
     * Mengambil daftar sparepart dengan filter dan pagination.
     */
    public function getFilteredSpareparts(array $filters, int $perPage = 10)
    {
        $query = Sparepart::query();

        // Filter untuk item yang dihapus (trash)
        if (($filters['trash'] ?? '') === 'true') {
            $query->onlyTrashed();
        }

        // Pencarian berdasarkan nama, part number, atau brand
        if (! empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%'.$filters['search'].'%')
                    ->orWhere('part_number', 'like', '%'.$filters['search'].'%')
                    ->orWhere('brand', 'like', '%'.$filters['search'].'%');
            });
        }

        // Penerapan filter eksak
        $this->applyExactFilter($query, 'brand', $filters['brand'] ?? null, __('messages.all_brands'));
        $this->applyExactFilter($query, 'category', $filters['category'] ?? null, __('messages.all_categories'));
        $this->applyExactFilter($query, 'location', $filters['location'] ?? null, __('messages.all_locations'));
        $this->applyExactFilter($query, 'color', $filters['color'] ?? null, __('messages.all_colors'));
        $this->applyExactFilter($query, 'type', $filters['type'] ?? null, __('messages.all_types'));
        $this->applyExactFilter($query, 'condition', $filters['condition'] ?? null, __('messages.all_conditions'));

        // Filter kategori khusus (stok rendah, jatuh tempo, tanpa harga)
        if (($filters['filter'] ?? '') === 'low_stock') {
            // Perhatian: NULL atau 0 pada minimum_stock dianggap tidak dipantau.
            $query->where('minimum_stock', '>', 0)
                ->whereColumn('stock', '<=', 'minimum_stock')
                ->where('condition', 'Baik');
        } elseif (($filters['filter'] ?? '') === 'overdue') {
            $query->whereHas('borrowings', function ($q) {
                $q->where('status', 'borrowed')
                    ->where('expected_return_at', '<', now());
            });
        } elseif (($filters['filter'] ?? '') === 'borrowed') {
            $query->whereHas('borrowings', function ($q) {
                $q->where('status', 'borrowed');
            });
        } elseif (($filters['filter'] ?? '') === 'no_price') {
            $query->where(function ($q) {
                $q->whereNull('price')->orWhere('price', '<=', 0);
            });
        } elseif (($filters['filter'] ?? '') === 'problematic') {
            // Filter barang yang rusak atau hilang (Hanya Superadmin & Admin)
            if (auth()->check() && in_array(auth()->user()->role, [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])) {
                $query->whereIn('condition', ['Rusak', 'Hilang']);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $this->applySorting($query, $filters['sort'] ?? null);

        return $query->paginate($perPage)->appends($filters);
    }

    /**
     * Membuat sparepart baru. Jika ditemukan aset yang identik, stok akan digabungkan.
     */
    public function createSparepart(array $data)
    {
        return DB::transaction(function () use ($data) {
            $existingItem = $this->findExactDuplicate($data);

            if ($existingItem) {
                // Jika aset identik ditemukan, lakukan penggabungan stok (merging)
                if ($data['stock'] > 0) {
                    $existingItem->stock += $data['stock'];
                    $existingItem->save();

                    $message = __('messages.stock_merged', [
                        'name' => $existingItem->name,
                        'part_number' => $existingItem->part_number,
                    ]);

                    StockLog::create([
                        'sparepart_id' => $existingItem->id,
                        'user_id' => auth()->id(),
                        'type' => 'masuk',
                        'quantity' => $data['stock'],
                        'reason' => __('messages.log_stock_added_duplicate'),
                        'status' => 'approved',
                        'approved_by' => auth()->id(),
                    ]);

                    $this->logActivity('Stok Diupdate', $message);
                    $this->clearCache();
                    $this->broadcastUpdate($existingItem, 'updated');

                    return ['status' => 'merged', 'message' => $message, 'data' => $existingItem];
                } else {
                    $message = __('messages.stock_zero_duplicate', [
                        'name' => $existingItem->name,
                        'part_number' => $existingItem->part_number,
                    ]);

                    return ['status' => 'error_zero_stock', 'message' => $message, 'data' => $existingItem];
                }
            }

            // Proses pembuatan item baru
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $data['image'] = $this->imageOptimizer->optimizeAndSave($data['image'], 'spareparts');
            } elseif (! empty($data['existing_image'])) {
                $existingPath = $data['existing_image'];
                if (Storage::disk('public')->exists($existingPath)) {
                    $extension = pathinfo($existingPath, PATHINFO_EXTENSION);
                    $newPath = 'spareparts/'.Str::random(40).'.'.$extension;
                    Storage::disk('public')->copy($existingPath, $newPath);
                    $data['image'] = $newPath;
                }
            }

            $sparepart = Sparepart::create($data);
            $this->qrCodeService->generate($sparepart);

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

            $this->logActivity('Sparepart Dibuat', $message);
            if ($sparepart->location) $this->syncLocations($sparepart->location);
            if ($sparepart->category) $this->syncCategories($sparepart->category);
            if ($sparepart->brand) $this->syncBrands($sparepart->brand);
            $this->clearCache();
            $this->broadcastUpdate($sparepart, 'created');

            return ['status' => 'created', 'message' => $message, 'data' => $sparepart];
        });
    }

    /**
     * Memperbarui data sparepart dan mengelola regenerasi QR jika identitas berubah.
     */
    public function updateSparepart(Sparepart $sparepart, array $data)
    {
        return DB::transaction(function () use ($sparepart, $data) {
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                if ($sparepart->image && Storage::disk('public')->exists($sparepart->image)) {
                    Storage::disk('public')->delete($sparepart->image);
                }
                $data['image'] = $this->imageOptimizer->optimizeAndSave($data['image'], 'spareparts');
            }

            $sparepart->fill($data);

            // Regenerasi QR hanya jika part_number berubah untuk efisiensi
            if ($sparepart->wasChanged('part_number') || ! $sparepart->qr_code_path) {
                if ($sparepart->getOriginal('qr_code_path') && Storage::disk('public')->exists($sparepart->getOriginal('qr_code_path'))) {
                    Storage::disk('public')->delete($sparepart->getOriginal('qr_code_path'));
                }
                $this->qrCodeService->generate($sparepart);
            }

            // Notifikasi stok rendah jika melampaui ambang batas
            if ($sparepart->minimum_stock > 0 && $sparepart->stock <= $sparepart->minimum_stock && $sparepart->wasChanged('stock')) {
                $admins = User::whereIn('role', [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])->get();
                Notification::send($admins, new LowStockNotification($sparepart));
            }

            $changes = [];
            if ($sparepart->isDirty()) {
                foreach ($sparepart->getDirty() as $key => $value) {
                    $original = $sparepart->getOriginal($key);
                    $changes[$key] = ['old' => $original, 'new' => $value];
                }
            }

            $sparepart->save();

            if ($sparepart->isDirty('location') && $sparepart->location) {
                $this->syncLocations($sparepart->location);
            }
            if ($sparepart->isDirty('category') && $sparepart->category) {
                $this->syncCategories($sparepart->category);
            }
            if ($sparepart->isDirty('brand') && $sparepart->brand) {
                $this->syncBrands($sparepart->brand);
            }

            $this->logActivity('Sparepart Diperbarui', __('messages.log_item_updated', ['name' => $sparepart->name, 'part_number' => $sparepart->part_number]), $changes);
            $this->clearCache();
            $this->broadcastUpdate($sparepart, 'updated');

            // Notifikasi stok rendah jika melampaui ambang batas kritis
            if ($sparepart->minimum_stock > 0 && $sparepart->stock <= $sparepart->minimum_stock) {
                $admins = User::whereIn('role', [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])->get();
                Notification::send($admins, new LowStockNotification($sparepart));

                $severity = $sparepart->stock === 0 ? 'depleted' : 'critical';
                try {
                    broadcast(new \App\Events\StockCriticalEvent($sparepart, $severity));
                } catch (\Throwable $e) {
                }

            } elseif ($sparepart->minimum_stock > 0 && $sparepart->stock <= (int) round($sparepart->minimum_stock * 1.5) && $sparepart->wasChanged('stock')) {
                // Notifikasi approaching: stok menuju minimum (antara 100%-150% dari minimum)
                $admins = User::whereIn('role', [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])->get();
                Notification::send($admins, new ApproachingStockNotification($sparepart));
            }

            return ['status' => 'updated', 'message' => __('messages.item_updated'), 'data' => $sparepart];
        });
    }

    /**
     * Menghapus sparepart secara lunak (Soft Delete).
     */
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

    /**
     * Memulihkan sparepart yang sebelumnya dihapus secara lunak.
     */
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

    /**
     * Menghapus sparepart secara permanen beserta file asetnya.
     */
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

    /**
     * Mengosongkan seluruh isi tong sampah.
     */
    public function forceDeleteAllSpareparts()
    {
        return DB::transaction(function () {
            $spareparts = Sparepart::onlyTrashed()->get();

            if ($spareparts->isEmpty()) {
                return ['status' => 'empty', 'message' => __('messages.trash_empty')];
            }

            foreach ($spareparts as $sparepart) {
                if ($sparepart->qr_code_path && Storage::disk('public')->exists($sparepart->qr_code_path)) {
                    Storage::disk('public')->delete($sparepart->qr_code_path);
                }
                if ($sparepart->image && Storage::disk('public')->exists($sparepart->image)) {
                    Storage::disk('public')->delete($sparepart->image);
                }
                $sparepart->forceDelete();
            }

            $this->logActivity('Tong Sampah Dikosongkan', __('messages.log_trash_cleared', ['count' => $spareparts->count()]));
            $this->clearCache();

            return ['status' => 'all_deleted', 'message' => __('messages.trash_cleared')];
        });
    }

    /**
     * Memulihkan banyak item sekaligus dari tong sampah.
     */
    public function bulkRestore(array $ids)
    {
        return DB::transaction(function () use ($ids) {
            $count = Sparepart::onlyTrashed()->whereIn('id', $ids)->count();
            if ($count === 0) {
                return ['status' => 'empty', 'message' => __('messages.no_item_selected')];
            }

            Sparepart::onlyTrashed()->whereIn('id', $ids)->restore();

            $this->logActivity('Pemulihan Massal', __('messages.log_bulk_restored', ['count' => $count]));
            $this->clearCache();

            return ['status' => 'success', 'message' => __('messages.bulk_restored', ['count' => $count])];
        });
    }

    /**
     * Menghapus banyak item secara permanen sekaligus.
     */
    public function bulkForceDelete(array $ids)
    {
        return DB::transaction(function () use ($ids) {
            $spareparts = Sparepart::onlyTrashed()->whereIn('id', $ids)->get();
            if ($spareparts->isEmpty()) {
                return ['status' => 'empty', 'message' => __('messages.no_item_selected')];
            }

            foreach ($spareparts as $sparepart) {
                if ($sparepart->qr_code_path && Storage::disk('public')->exists($sparepart->qr_code_path)) {
                    Storage::disk('public')->delete($sparepart->qr_code_path);
                }
                if ($sparepart->image && Storage::disk('public')->exists($sparepart->image)) {
                    Storage::disk('public')->delete($sparepart->image);
                }
                $sparepart->forceDelete();
            }

            $this->logActivity('Hapus Permanen Massal', __('messages.log_bulk_deleted_force', ['count' => $spareparts->count()]));
            $this->clearCache();

            return ['status' => 'success', 'message' => __('messages.bulk_force_deleted', ['count' => $spareparts->count()])];
        });
    }

    /**
     * Mengambil opsi filter dropdown yang tersedia (dengan sistem caching).
     */
    public function getDropdownOptions()
    {
        return [
            'categories' => Cache::remember('inventory_categories', 3600, function () {
                return \App\Models\Category::orderBy('name')->pluck('name');
            }),
            'brands' => Cache::remember('inventory_brands', 3600, function () {
                return \App\Models\Brand::orderBy('name')->pluck('name');
            }),
            'locations' => Cache::remember('inventory_locations', 3600, function () {
                return \App\Models\Location::orderBy('name')->pluck('name');
            }),
            'colors' => Cache::remember('inventory_colors', 3600, fn () => Sparepart::whereNotNull('color')->select('color')->distinct()->pluck('color')),
            'units' => Cache::remember('inventory_units', 3600, fn () => Sparepart::whereNotNull('unit')->select('unit')->distinct()->pluck('unit')),
            'names' => Cache::remember('inventory_names', 3600, fn () => Sparepart::select('name')->distinct()->pluck('name')),
            'partNumbers' => Cache::remember('inventory_part_numbers', 3600, fn () => Sparepart::select('part_number')->distinct()->pluck('part_number')),
            'conditions' => Cache::remember('inventory_conditions', 3600, fn () => Sparepart::whereNotNull('condition')->select('condition')->distinct()->pluck('condition')),
        ];
    }

    public function syncLocations(string $locationName): void
    {
        if (auth()->check() && auth()->user()->role === \App\Enums\UserRole::SUPERADMIN) {
            \App\Models\Location::firstOrCreate(['name' => $locationName]);
            $this->clearCache();
        }
    }

    public function syncCategories(string $categoryName): void
    {
        if (auth()->check() && in_array(auth()->user()->role, [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])) {
            \App\Models\Category::firstOrCreate(['name' => $categoryName]);
            $this->clearCache();
        }
    }

    public function syncBrands(string $brandName): void
    {
        if (auth()->check() && in_array(auth()->user()->role, [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])) {
            \App\Models\Brand::firstOrCreate(['name' => $brandName]);
            $this->clearCache();
        }
    }

    /**
     * Invalidasi seluruh cache terkait inventaris dan dashboard.
     */
    public function clearCache()
    {
        Cache::forget('inventory_categories');
        Cache::forget('inventory_brands');
        Cache::forget('inventory_locations');
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

    /**
     * Memeriksa apakah pembaruan data akan menyebabkan duplikasi dengan aset lain.
     */
    public function checkUpdateDuplicate(Sparepart $currentItem, array $data)
    {
        $checkData = array_merge($currentItem->toArray(), $data);

        $query = Sparepart::where('id', '!=', $currentItem->id)
            ->where('part_number', $checkData['part_number'])
            ->where('name', $checkData['name'])
            ->where('brand', $checkData['brand'])
            ->where('category', $checkData['category'])
            ->where('location', $checkData['location'])
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

    /**
     * Menggabungkan dua sparepart identik (misal setelah pembaruan lokasi/kondisi).
     */
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

            $this->logActivity('Penggabungan Sparepart', $message);
            $this->clearCache();
            $this->broadcastUpdate($target, 'updated');

            return ['status' => 'merged', 'message' => $message, 'data' => $target];
        });
    }

    /**
     * Mencari duplikat aset yang benar-benar identik di database.
     */
    private function findExactDuplicate(array $data)
    {
        $existingItemQuery = Sparepart::where('part_number', $data['part_number'])
            ->where('name', $data['name'])
            ->where('brand', $data['brand'])
            ->where('category', $data['category'])
            ->where('location', $data['location'])
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

    /**
     * Membuat data peminjaman baru dan melakukan pengurangan stok secara aman.
     */
    public function createBorrowing(Sparepart $sparepart, array $data)
    {
        return DB::transaction(function () use ($sparepart, $data) {
            $sparepart = Sparepart::where('id', $sparepart->id)->lockForUpdate()->first();

            if ($sparepart->stock < $data['quantity']) {
                throw new \Exception(__('messages.insufficient_stock'));
            }

            $borrowing = Borrowing::create([
                'sparepart_id' => $sparepart->id,
                'user_id' => auth()->id(),
                'borrower_name' => auth()->user()->name,
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
            if ($sparepart->minimum_stock > 0 && $sparepart->stock <= $sparepart->minimum_stock) {
                $admins = User::whereIn('role', [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])->get();
                Notification::send($admins, new LowStockNotification($sparepart));

                $severity = $sparepart->stock === 0 ? 'depleted' : 'critical';
                try {
                    broadcast(new \App\Events\StockCriticalEvent($sparepart, $severity));
                } catch (\Throwable $e) {
                }

            } elseif ($sparepart->minimum_stock > 0 && $sparepart->stock <= (int) round($sparepart->minimum_stock * 1.5)) {
                $admins = User::whereIn('role', [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])->get();
                Notification::send($admins, new ApproachingStockNotification($sparepart));
            }

            $this->logActivity('Peminjaman Barang', "Meminjam {$data['quantity']} {$sparepart->unit} '{$sparepart->name}'.");
            $this->clearCache();
            $this->broadcastUpdate($sparepart, 'borrowing', __('messages.realtime_borrowed', ['user' => auth()->user()->name, 'qty' => $data['quantity'], 'name' => $sparepart->name]));

            return ['status' => 'created', 'borrowing' => $borrowing];
        });
    }

    /**
     * Memproses pengembalian barang dan menyesuaikan kondisi stok aset.
     */
    public function returnBorrowing(Borrowing $borrowing, array $data, array $photos = [])
    {
        return DB::transaction(function () use ($borrowing, $data, $photos) {
            $qty = $data['return_quantity'];
            $condition = $data['return_condition'];
            $originalSparepart = $borrowing->sparepart;

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

                $this->logActivity('Pengembalian Barang (Baik)', "Mengembalikan {$qty} unit '{$originalSparepart->name}' dalam kondisi Baik.");

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

                $this->logActivity('Pengembalian Barang ('.$translatedCondition.')', "Mengembalikan {$qty} unit '{$originalSparepart->name}' dalam kondisi {$translatedCondition}.");
            }

            $this->clearCache();
            $this->broadcastUpdate($originalSparepart, 'returned', __('messages.realtime_returned', ['user' => auth()->user()->name, 'qty' => $qty, 'name' => $originalSparepart->name]));

            return ['status' => 'success'];
        });
    }

    /**
     * Menyetujui atau menolak permohonan penyesuaian stok (Approval Flow).
     */
    public function approveStockRequest(StockLog $stockLog, string $status, ?string $rejectionReason = null)
    {
        if ($stockLog->status !== 'pending') {
            throw new \Exception('Pengajuan ini sudah diproses sebelumnya.');
        }

        return DB::transaction(function () use ($stockLog, $status, $rejectionReason) {
            if ($status === 'approved') {
                $sparepart = Sparepart::where('id', $stockLog->sparepart_id)->lockForUpdate()->first();

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
                } elseif ($sparepart->minimum_stock > 0 && $sparepart->stock <= (int) round($sparepart->minimum_stock * 1.5)) {
                    $admins = User::whereIn('role', [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])->get();
                    Notification::send($admins, new ApproachingStockNotification($sparepart));
                }
            }

            $updateData = [
                'status'      => $status,
                'approved_by' => auth()->id(),
            ];
            if ($status === 'rejected' && $rejectionReason) {
                $updateData['rejection_reason'] = $rejectionReason;
            }
            $stockLog->update($updateData);

            $statusText = $status === 'approved' ? 'disetujui' : 'ditolak';
            $this->logActivity(
                'Persetujuan Stok',
                "Pengajuan stok {$stockLog->type} untuk '{$stockLog->sparepart->name}' sejumlah {$stockLog->quantity} telah {$statusText}."
            );

            // Broadcast real-time stock approval processing (remove from list)
            try {
                broadcast(new \App\Events\StockApprovalUpdatedEvent($stockLog->fresh(), 'processed'))->toOthers();
            } catch (\Throwable $e) {
            }

            // Notifikasi balik ke pemohon (Operator/Admin) mengenai hasil approval
            $requester = $stockLog->user;
            if ($requester) {
                $message = __('ui.notification_stock_request_body', [
                    'type'   => $stockLog->type,
                    'name'   => $stockLog->sparepart->name,
                    'status' => $statusText,
                ]);
                Notification::send($requester, new StockRequestNotification($stockLog, $message));
            }

            return ['status' => 'success'];
        });
    }

    /**
     * Melakukan broadcast perubahan data ke seluruh client yang terhubung secara real-time.
     */
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
