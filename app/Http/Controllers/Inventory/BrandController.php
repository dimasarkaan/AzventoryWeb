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
        ]);

        $oldName = $brand->name;
        $newName = $request->name;

        DB::transaction(function () use ($brand, $oldName, $newName) {
            // Update master table
            $brand->update(['name' => $newName]);

            // Bulk update spareparts table (the actual string)
            Sparepart::where('brand', $oldName)->update(['brand' => $newName]);
        });

        Cache::forget('inventory_brands');

        $this->logActivity('Merk Diperbarui', "Nama merk diubah dari '{$oldName}' menjadi '{$newName}'.");

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
