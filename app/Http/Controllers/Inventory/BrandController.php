<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Traits\ActivityLogger;

class BrandController extends Controller
{
    use ActivityLogger;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::orderBy('name')->get()->map(function ($brand) {
            return [
                'id' => $brand->id,
                'name' => $brand->name,
                'is_active' => (bool) $brand->is_active,
                'items_count' => Sparepart::where('brand', $brand->name)->count(),
            ];
        });

        return response()->json($brands);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191|unique:brands,name',
        ]);

        $brand = Brand::create(['name' => $request->name]);
        Cache::forget('inventory_brands');

        $this->logActivity('Merk Dibuat', "Merk baru '{$brand->name}' ditambahkan.");

        return response()->json([
            'message' => 'Merk baru berhasil ditambahkan.',
            'brand' => $brand
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:191|unique:brands,name,' . $brand->id,
            'is_active' => 'sometimes|boolean',
        ]);

        $oldName = $brand->name;
        $newName = $request->name;

        DB::transaction(function () use ($brand, $oldName, $newName, $request) {
            // Update master table
            $updateData = ['name' => $newName];
            if ($request->has('is_active')) {
                $updateData['is_active'] = $request->is_active;
            }
            $brand->update($updateData);

            // Bulk update spareparts table (the actual string)
            if ($oldName !== $newName) {
                Sparepart::where('brand', $oldName)->update(['brand' => $newName]);
            }
        });

        Cache::forget('inventory_brands');

        $hasChanged = ($oldName !== $newName) || ($request->has('is_active') && $brand->is_active != $request->is_active);

        if ($hasChanged) {
            $changes = [];
            if ($oldName !== $newName) {
                $changes['name'] = ['old' => $oldName, 'new' => $newName];
            }
            if ($request->has('is_active') && $brand->getOriginal('is_active') != $request->is_active) {
                $changes['is_active'] = ['old' => (bool)$brand->getOriginal('is_active'), 'new' => (bool)$request->is_active];
            }

            // Pesan lebih detail: sebutkan apa yang berubah
            if ($oldName !== $newName && $request->has('is_active') && $brand->getOriginal('is_active') != $request->is_active) {
                $statusText = $request->is_active ? 'Aktif' : 'Non-aktif';
                $logMessage = "Nama merk diubah dari '{$oldName}' menjadi '{$newName}' dan status diubah menjadi {$statusText}.";
            } elseif ($oldName !== $newName) {
                $logMessage = "Nama merk diubah dari '{$oldName}' menjadi '{$newName}'.";
            } else {
                $statusText = $request->is_active ? 'Aktif' : 'Non-aktif';
                $logMessage = "Status merk '{$newName}' diubah menjadi {$statusText}.";
            }

            $this->logActivity('Merk Diperbarui', $logMessage, $changes);
        }

        return response()->json([
            'message' => 'Merk berhasil diperbarui.',
            'brand' => $brand
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $count = Sparepart::where('brand', $brand->name)->count();

        if ($count > 0) {
            return response()->json([
                'message' => "Tidak dapat menghapus. Masih ada {$count} barang di merk ini. Ubah terlebih dahulu."
            ], 422);
        }

        $brand->delete();
        Cache::forget('inventory_brands');

        $this->logActivity('Merk Dihapus', "Merk '{$brand->name}' dihapus.");

        return response()->json([
            'message' => 'Merk berhasil dihapus.'
        ]);
    }
}
