<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

// Controller khusus untuk mengelola Master Data Merek (Merk Barang/Vendor).
// Berperan sebagai jembatan API untuk melayani aksi Tambah, Edit, dan Hapus dari tampilan web.
class BrandController extends Controller
{
    use ActivityLogger;

    // Menampilkan seluruh daftar Merek yang ada beserta hitungan jumlah barang di tiap Mereknya
    public function index()
    {
        $brands = Brand::withCount('spareparts')->orderBy('name')->get()->map(function ($brand) {
            return [
                'id' => $brand->id,
                'name' => $brand->name,
                'is_active' => (bool) $brand->is_active,
                'items_count' => $brand->spareparts_count,
            ];
        });

        return response()->json($brands);
    }

    // Menyimpan data Merek baru ke dalam database
    public function store(Request $request)
    {
        $this->authorize('create', Brand::class);

        $request->validate([
            'name' => 'required|string|max:191|unique:brands,name',
        ]);

        $brand = Brand::create(['name' => $request->name]);
        Cache::forget('inventory_brands');
        Cache::forget('inventory_brand_options');

        $this->logActivity('Merk Dibuat', "Merk baru '{$brand->name}' ditambahkan.");

        return response()->json([
            'message' => 'Merk baru berhasil ditambahkan.',
            'brand' => $brand,
        ], 201);
    }

    // Mengedit/Memperbarui informasi nama Merek atau mengganti statusnya (Aktif/Non-aktif)
    public function update(Request $request, Brand $brand)
    {
        $this->authorize('update', $brand);

        $request->validate([
            'name' => 'required|string|max:191|unique:brands,name,'.$brand->id,
            'is_active' => 'sometimes|boolean',
        ]);

        $oldName = $brand->name;
        $newName = $request->name;
        $oldActive = (bool) $brand->is_active;
        $newActive = $request->has('is_active') ? (bool) $request->is_active : $oldActive;

        $hasChanged = ($oldName !== $newName) || ($oldActive !== $newActive);

        DB::transaction(function () use ($brand, $newName, $request) {
            // Update master table
            $updateData = ['name' => $newName];
            if ($request->has('is_active')) {
                $updateData['is_active'] = $request->is_active;
            }
            $brand->update($updateData);
            // Tidak perlu bulk-update spareparts lagi karena relasi via FK (id tetap sama)
        });

        Cache::forget('inventory_brands');
        Cache::forget('inventory_brand_options');

        if ($hasChanged) {
            $changes = [];
            if ($oldName !== $newName) {
                $changes['name'] = ['old' => $oldName, 'new' => $newName];
            }
            if ($oldActive !== $newActive) {
                $changes['is_active'] = ['old' => $oldActive, 'new' => $newActive];
            }

            // Pesan lebih detail: sebutkan apa yang berubah
            if ($oldName !== $newName && $oldActive !== $newActive) {
                $statusText = $newActive ? 'Aktif' : 'Non-aktif';
                $logMessage = "Nama merk diubah dari '{$oldName}' menjadi '{$newName}' dan status diubah menjadi {$statusText}.";
            } elseif ($oldName !== $newName) {
                $logMessage = "Nama merk diubah dari '{$oldName}' menjadi '{$newName}'.";
            } else {
                $statusText = $newActive ? 'Aktif' : 'Non-aktif';
                $logMessage = "Status merk '{$newName}' diubah menjadi {$statusText}.";
            }

            $this->logActivity('Merk Diperbarui', $logMessage, $changes);
        }

        return response()->json([
            'message' => 'Merk berhasil diperbarui.',
            'brand' => $brand,
        ]);
    }

    // Menghapus data Merek selamanya dari sistem
    public function destroy(Brand $brand)
    {
        $this->authorize('delete', $brand);

        $count = $brand->spareparts()->withTrashed()->count();

        if ($count > 0) {
            return response()->json([
                'message' => "Tidak dapat menghapus. Masih ada {$count} barang di merk ini. Ubah terlebih dahulu.",
            ], 422);
        }

        $brand->delete();
        Cache::forget('inventory_brands');
        Cache::forget('inventory_brand_options');

        $this->logActivity('Merk Dihapus', "Merk '{$brand->name}' dihapus.");

        return response()->json([
            'message' => 'Merk berhasil dihapus.',
        ]);
    }
}
