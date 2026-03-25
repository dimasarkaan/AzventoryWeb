<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Traits\ActivityLogger;

class CategoryController extends Controller
{
    use ActivityLogger;

    public function index()
    {
        $categories = Category::orderBy('name')->get()->map(function ($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'is_active' => (bool) $cat->is_active,
                'items_count' => Sparepart::where('category', $cat->name)->count(),
            ];
        });

        return response()->json($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create(['name' => $request->name]);
        Cache::forget('inventory_categories');

        $this->logActivity('Kategori Dibuat', "Kategori baru '{$category->name}' ditambahkan.");

        return response()->json([
            'message' => 'Kategori baru berhasil ditambahkan.',
            'category' => $category
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'is_active' => 'sometimes|boolean',
        ]);

        $category = Category::findOrFail($id);
        $oldName = $category->name;
        $newName = $request->name;

        DB::transaction(function () use ($category, $oldName, $newName, $request) {
            $updateData = ['name' => $newName];
            if ($request->has('is_active')) {
                $updateData['is_active'] = $request->is_active;
            }
            $category->update($updateData);

            // Update all spareparts that use this category string
            if ($oldName !== $newName) {
                Sparepart::where('category', $oldName)->update(['category' => $newName]);
            }
        });

        Cache::forget('inventory_categories');

        $hasChanged = ($oldName !== $newName) || ($request->has('is_active') && $category->is_active != $request->is_active);

        if ($hasChanged) {
            $changes = [];
            if ($oldName !== $newName) {
                $changes['name'] = ['old' => $oldName, 'new' => $newName];
            }
            if ($request->has('is_active') && $category->getOriginal('is_active') != $request->is_active) {
                $changes['is_active'] = ['old' => (bool)$category->getOriginal('is_active'), 'new' => (bool)$request->is_active];
            }

            // Pesan lebih detail: sebutkan apa yang berubah
            if ($oldName !== $newName && $request->has('is_active') && $category->getOriginal('is_active') != $request->is_active) {
                $statusText = $request->is_active ? 'Aktif' : 'Non-aktif';
                $logMessage = "Nama kategori diubah dari '{$oldName}' menjadi '{$newName}' dan status diubah menjadi {$statusText}.";
            } elseif ($oldName !== $newName) {
                $logMessage = "Nama kategori diubah dari '{$oldName}' menjadi '{$newName}'.";
            } else {
                $statusText = $request->is_active ? 'Aktif' : 'Non-aktif';
                $logMessage = "Status kategori '{$newName}' diubah menjadi {$statusText}.";
            }
            
            $this->logActivity('Kategori Diperbarui', $logMessage, $changes);
        }

        return response()->json(['message' => 'Kategori berhasil diperbarui.']);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Check if category is in use
        $count = Sparepart::where('category', $category->name)->count();

        if ($count > 0) {
            return response()->json([
                'message' => "Tidak dapat menghapus. Masih ada $count barang dalam kategori ini. Kosongkan terlebih dahulu."
            ], 422);
        }

        $category->delete();
        Cache::forget('inventory_categories');

        $this->logActivity('Kategori Dihapus', "Kategori '{$category->name}' dihapus.");

        return response()->json(['message' => 'Kategori berhasil dihapus.']);
    }
}
