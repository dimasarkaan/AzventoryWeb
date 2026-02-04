<?php

namespace App\Services;

use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use App\Notifications\LowStockNotification;
use App\Traits\ActivityLogger;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InventoryService
{
    use ActivityLogger;

    protected $qrCodeService;

    public function __construct(ImageOptimizationService $imageOptimizer, QrCodeService $qrCodeService)
    {
        $this->imageOptimizer = $imageOptimizer;
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Get filtered spareparts with pagination.
     */
    public function getFilteredSpareparts(array $filters, int $perPage = 10)
    {
        $query = Sparepart::query();

        if (($filters['trash'] ?? '') === 'true') {
            $query->onlyTrashed();
        }

        // Search Scope
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('part_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('brand', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Filters
        $this->applyExactFilter($query, 'brand', $filters['brand'] ?? null, 'Semua Merk');
        $this->applyExactFilter($query, 'category', $filters['category'] ?? null, 'Semua Kategori');
        $this->applyExactFilter($query, 'location', $filters['location'] ?? null, 'Semua Lokasi');
        $this->applyExactFilter($query, 'color', $filters['color'] ?? null, 'Semua Warna');

        // Special Filter
        if (($filters['filter'] ?? '') === 'low_stock') {
            $query->whereColumn('stock', '<=', 'minimum_stock');
        }

        // Sorting
        $this->applySorting($query, $filters['sort'] ?? null);

        return $query->paginate($perPage)->appends($filters);
    }

    /**
     * Create a new sparepart or merge stock if duplicate.
     */
    public function createSparepart(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Check for exact duplicate
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

            $existingItem = $existingItemQuery->lockForUpdate()->first();

            if ($existingItem) {
                // DUPLICATE FOUND: Merge Stock
                if ($data['stock'] > 0) {
                    $existingItem->stock += $data['stock'];
                    $existingItem->save();

                    $message = "Stok sparepart '{$existingItem->name}' (PN: {$existingItem->part_number}) berhasil ditambahkan ke item yang sudah ada.";

                    // Log Stock Addition
                    StockLog::create([
                        'sparepart_id' => $existingItem->id,
                        'user_id' => auth()->id(),
                        'type' => 'masuk',
                        'quantity' => $data['stock'],
                        'reason' => 'Penambahan stok (Duplicate Entry)',
                        'status' => 'approved',
                        'approved_by' => auth()->id(),
                        'approved_at' => now(),
                    ]);

                    $this->logActivity('Stok Diupdate', $message);
                    $this->clearCache();

                    return ['status' => 'merged', 'message' => $message, 'data' => $existingItem];
                } else {
                    // Duplicate input but 0 stock - No Action
                    $message = "Item '{$existingItem->name}' (PN: {$existingItem->part_number}) sudah ada di inventaris dan stok input adalah 0. Silakan periksa kembali jumlah stok.";
                    return ['status' => 'error_zero_stock', 'message' => $message, 'data' => $existingItem];
                }
            }

            // NEW ITEM
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $data['image'] = $this->imageOptimizer->optimizeAndSave($data['image'], 'spareparts');
            } elseif (!empty($data['existing_image'])) {
                // Copy existing image logic
                $existingPath = $data['existing_image'];
                if (Storage::disk('public')->exists($existingPath)) {
                    $extension = pathinfo($existingPath, PATHINFO_EXTENSION);
                    $newPath = 'spareparts/' . Str::random(40) . '.' . $extension;
                    Storage::disk('public')->copy($existingPath, $newPath);
                    $data['image'] = $newPath;
                }
            }

            $sparepart = Sparepart::create($data);

            // Generate QR Code via Service
            $this->qrCodeService->generate($sparepart);

            // Log Initial Stock
            if ($sparepart->stock > 0) {
                StockLog::create([
                    'sparepart_id' => $sparepart->id,
                    'user_id' => auth()->id(),
                    'type' => 'masuk',
                    'quantity' => $sparepart->stock,
                    'reason' => 'Stok awal (Item baru)',
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);
            }

            $message = "Sparepart '{$sparepart->name}' (PN: {$sparepart->part_number}) telah ditambahkan.";
            $this->logActivity('Sparepart Dibuat', $message);
            $this->clearCache();

            return ['status' => 'created', 'message' => $message, 'data' => $sparepart];
        });
    }

    /**
     * Update an existing sparepart.
     */
    public function updateSparepart(Sparepart $sparepart, array $data)
    {
        return DB::transaction(function () use ($sparepart, $data) {
            // Handle Image Upload
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                if ($sparepart->image && Storage::disk('public')->exists($sparepart->image)) {
                    Storage::disk('public')->delete($sparepart->image);
                }
                $data['image'] = $this->imageOptimizer->optimizeAndSave($data['image'], 'spareparts');
            }

            $sparepart->fill($data);

            // Check if QR regeneration needed
            if ($sparepart->wasChanged('part_number') || !$sparepart->qr_code_path) {
                if ($sparepart->getOriginal('qr_code_path') && Storage::disk('public')->exists($sparepart->getOriginal('qr_code_path'))) {
                    Storage::disk('public')->delete($sparepart->getOriginal('qr_code_path'));
                }
                $this->qrCodeService->generate($sparepart);
            }

            // Low Stock Notification
            if ($sparepart->minimum_stock > 0 && $sparepart->stock <= $sparepart->minimum_stock && $sparepart->wasChanged('stock')) {
                $admins = User::whereIn('role', ['superadmin', 'admin'])->get();
                Notification::send($admins, new LowStockNotification($sparepart));
            }

            // Logging Changes
            $changes = [];
            if ($sparepart->isDirty()) {
                foreach ($sparepart->getDirty() as $key => $value) {
                    $original = $sparepart->getOriginal($key);
                    $changes[$key] = ['old' => $original, 'new' => $value];
                }
            }

            $sparepart->save();
            $this->logActivity('Sparepart Diperbarui', "Data sparepart '{$sparepart->name}' (PN: {$sparepart->part_number}) telah diperbarui.", $changes);
            $this->clearCache();

            return ['status' => 'updated', 'message' => 'Data sparepart berhasil diperbarui.', 'data' => $sparepart];
        });
    }

    /**
     * Soft delete a sparepart.
     */
    public function deleteSparepart(Sparepart $sparepart)
    {
        return DB::transaction(function () use ($sparepart) {
            $this->logActivity('Sparepart Dihapus', "Sparepart '{$sparepart->name}' (PN: {$sparepart->part_number}) telah dipindahkan ke tong sampah.");
            $sparepart->delete();
            $this->clearCache();

            return ['status' => 'deleted', 'message' => 'Data sparepart berhasil dipindahkan ke tong sampah.'];
        });
    }

    /**
     * Restore a soft-deleted sparepart.
     */
    public function restoreSparepart($id)
    {
        return DB::transaction(function () use ($id) {
            $sparepart = Sparepart::onlyTrashed()->findOrFail($id);
            $sparepart->restore();
            
            $this->logActivity('Sparepart Dipulihkan', "Sparepart '{$sparepart->name}' (PN: {$sparepart->part_number}) telah dipulihkan dari tong sampah.");
            $this->clearCache();

            return ['status' => 'restored', 'message' => 'Data sparepart berhasil dipulihkan.'];
        });
    }

    /**
     * Permanently delete a sparepart.
     */
    public function forceDeleteSparepart($id)
    {
        return DB::transaction(function () use ($id) {
            $sparepart = Sparepart::onlyTrashed()->findOrFail($id);
            
            // Delete associated files
            if ($sparepart->qr_code_path && Storage::disk('public')->exists($sparepart->qr_code_path)) {
                Storage::disk('public')->delete($sparepart->qr_code_path);
            }
            if ($sparepart->image && Storage::disk('public')->exists($sparepart->image)) {
                Storage::disk('public')->delete($sparepart->image);
            }

            $sparepart->forceDelete();
            
            $this->logActivity('Sparepart Dihapus Permanen', "Sparepart '{$sparepart->name}' (PN: {$sparepart->part_number}) telah dihapus permanen.");
            $this->clearCache();

            return ['status' => 'force_deleted', 'message' => 'Data sparepart berhasil dihapus permanen.'];
        });
    }

    /**
     * Permanently delete all spareparts in trash.
     */
    public function forceDeleteAllSpareparts()
    {
        return DB::transaction(function () {
            $spareparts = Sparepart::onlyTrashed()->get();

            if ($spareparts->isEmpty()) {
                return ['status' => 'empty', 'message' => 'Tempat sampah sudah kosong.'];
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

            $this->logActivity('Tong Sampah Dikosongkan', "Semua item di tong sampah (" . $spareparts->count() . " item) telah dihapus permanen.");
            $this->clearCache();

            return ['status' => 'all_deleted', 'message' => 'Semua data di tong sampah berhasil dihapus permanen.'];
        });
    }

    /**
     * Bulk restore spareparts.
     */
    public function bulkRestore(array $ids)
    {
        return DB::transaction(function () use ($ids) {
            $count = Sparepart::onlyTrashed()->whereIn('id', $ids)->count();
            if ($count === 0) return ['status' => 'empty', 'message' => 'Tidak ada item yang dipilih.'];

            Sparepart::onlyTrashed()->whereIn('id', $ids)->restore();

            $this->logActivity('Bulk Restore', "$count item berhasil dipulihkan dari tong sampah.");
            $this->clearCache();

            return ['status' => 'success', 'message' => "$count item berhasil dipulihkan."];
        });
    }

    /**
     * Bulk force delete spareparts.
     */
    public function bulkForceDelete(array $ids)
    {
        return DB::transaction(function () use ($ids) {
            $spareparts = Sparepart::onlyTrashed()->whereIn('id', $ids)->get();
            if ($spareparts->isEmpty()) return ['status' => 'empty', 'message' => 'Tidak ada item yang dipilih.'];

            $count = $spareparts->count();

            foreach ($spareparts as $sparepart) {
                if ($sparepart->qr_code_path && Storage::disk('public')->exists($sparepart->qr_code_path)) {
                    Storage::disk('public')->delete($sparepart->qr_code_path);
                }
                if ($sparepart->image && Storage::disk('public')->exists($sparepart->image)) {
                    Storage::disk('public')->delete($sparepart->image);
                }
                $sparepart->forceDelete();
            }

            $this->logActivity('Bulk Force Delete', "$count item telah dihapus permanen dari tong sampah.");
            $this->clearCache();

            return ['status' => 'success', 'message' => "$count item berhasil dihapus permanen."];
        });
    }

    /**
     * Get unique values for dropdowns (Cached).
     */
    public function getDropdownOptions()
    {
        return [
            'categories' => Cache::remember('inventory_categories', 3600, fn() => Sparepart::select('category')->distinct()->pluck('category')),
            'brands' => Cache::remember('inventory_brands', 3600, fn() => Sparepart::whereNotNull('brand')->select('brand')->distinct()->pluck('brand')),
            'locations' => Cache::remember('inventory_locations', 3600, fn() => Sparepart::select('location')->distinct()->pluck('location')),
            'colors' => Cache::remember('inventory_colors', 3600, fn() => Sparepart::whereNotNull('color')->select('color')->distinct()->pluck('color')),
            'units' => Cache::remember('inventory_units', 3600, fn() => Sparepart::whereNotNull('unit')->select('unit')->distinct()->pluck('unit')),
            'names' => Cache::remember('inventory_names', 3600, fn() => Sparepart::select('name')->distinct()->pluck('name')),
            'partNumbers' => Cache::remember('inventory_part_numbers', 3600, fn() => Sparepart::select('part_number')->distinct()->pluck('part_number')),
        ];
    }

    /**
     * Clear inventory caches.
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
    }

    private function applyExactFilter(Builder $query, string $column, ?string $value, string $ignoreValue)
    {
        if ($value && $value !== $ignoreValue) {
            $query->where($column, $value);
        }
    }

    private function applySorting(Builder $query, ?string $sort)
    {
        if (!$sort) {
            $query->latest();
            return;
        }

        switch ($sort) {
            case 'name_asc': $query->orderBy('name', 'asc'); break;
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            case 'stock_asc': $query->orderBy('stock', 'asc'); break;
            case 'stock_desc': $query->orderBy('stock', 'desc'); break;
            case 'price_asc': $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'oldest': $query->oldest(); break;
            case 'newest': default: $query->latest(); break;
        }
    }
}
