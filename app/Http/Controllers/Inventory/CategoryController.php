<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

// Controller khusus untuk mengelola Master Data Kategori (misal: Komputer, Elektronik, dll).
// Berbentuk API Controller yang diakses menggunakan AJAX untuk operasi Tambah, Edit, dan Hapus.
class CategoryController extends Controller
{
    use ActivityLogger;

    // Menampilkan seluruh daftar kategori beserta jumlah barang di tiap kategorinya
    public function index()
    {
        // Tarik semua data kategori dan hitung otomatis relasinya (jumlah barang di kategori ini)
        $categories = Category::withCount('spareparts')->orderBy('name')->get()->map(function ($cat) {
            // Bungkus data ke dalam format Array agar rapi saat diterima oleh JavaScript di frontend
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'is_active' => (bool) $cat->is_active,
                'items_count' => $cat->spareparts_count,
            ];
        });

        // Kembalikan ke browser sebagai format JSON
        return response()->json($categories);
    }

    // Menyimpan data kategori baru ke dalam database
    public function store(Request $request)
    {
        $this->authorize('create', Category::class);

        // Validasi: Nama kategori wajib ada, berupa teks, dan tak boleh kembar (unique) dengan data yang sudah ada
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create(['name' => $request->name]);
        
        // Kosongkan ingatan (Cache) lama agar form Dropdown (pilihan kategori) di halaman Tambah Barang otomatis ter-update
        Cache::forget('inventory_categories');
        Cache::forget('inventory_category_options');

        // Catat aktivitas ini ke dalam sistem audit riwayat pengguna
        $this->logActivity('Kategori Dibuat', "Kategori baru '{$category->name}' ditambahkan.");

        // Kirim konfirmasi ke AJAX bahwa data berhasil dibuat (Kode 201)
        return response()->json([
            'message' => 'Kategori baru berhasil ditambahkan.',
            'category' => $category,
        ], 201);
    }

    // Memperbarui informasi kategori yang sudah ada (Nama atau Status Aktifnya)
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('update', $category);

        // Validasi: Nama tidak boleh kembar KECUALI dengan dirinya sendiri (id-nya)
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,'.$id,
            'is_active' => 'sometimes|boolean',
        ]);

        $category = Category::findOrFail($id);
        
        // Simpan data lama untuk dijadikan bahan perbandingan nanti
        $oldName = $category->name;
        $newName = $request->name;
        $oldActive = (bool) $category->is_active;
        $newActive = $request->has('is_active') ? (bool) $request->is_active : $oldActive;

        // Mengecek apakah pengguna benar-benar mengganti datanya, atau cuma sekedar pencet tombol Save tanpa merubah apa-apa
        $hasChanged = ($oldName !== $newName) || ($oldActive !== $newActive);

        // Bungkus dengan transaksi database agar pengeditan lebih aman (ACID)
        DB::transaction(function () use ($category, $newName, $request) {
            $updateData = ['name' => $newName];
            if ($request->has('is_active')) {
                $updateData['is_active'] = $request->is_active;
            }
            $category->update($updateData);
            // Catatan: Relasi dengan tabel 'spareparts' dijamin aman karena Foreign Key-nya menggunakan 'id', bukan 'nama'
        });

        // Hapus cache yang memuat daftar lama kategori
        Cache::forget('inventory_categories');
        Cache::forget('inventory_category_options');

        // Jika benar-benar ada perubahan, catat jejaknya sedetail mungkin
        if ($hasChanged) {
            $changes = [];
            if ($oldName !== $newName) {
                $changes['name'] = ['old' => $oldName, 'new' => $newName];
            }
            if ($oldActive !== $newActive) {
                $changes['is_active'] = ['old' => $oldActive, 'new' => $newActive];
            }

            // Pesan catatan harus pintar dan bisa membedakan jenis perubahannya (Ganti nama saja, atau ganti status juga?)
            if ($oldName !== $newName && $oldActive !== $newActive) {
                $statusText = $newActive ? 'Aktif' : 'Non-aktif';
                $logMessage = "Nama kategori diubah dari '{$oldName}' menjadi '{$newName}' dan status diubah menjadi {$statusText}.";
            } elseif ($oldName !== $newName) {
                $logMessage = "Nama kategori diubah dari '{$oldName}' menjadi '{$newName}'.";
            } else {
                $statusText = $newActive ? 'Aktif' : 'Non-aktif';
                $logMessage = "Status kategori '{$newName}' diubah menjadi {$statusText}.";
            }

            $this->logActivity('Kategori Diperbarui', $logMessage, $changes);
        }

        return response()->json(['message' => 'Kategori berhasil diperbarui.']);
    }

    // Menghapus data kategori dari sistem selamanya
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $this->authorize('delete', $category);

        // Cek Keamanan: Pastikan apakah masih ada barang di gudang yang memakai nama kategori ini (termasuk yang di tempat sampah)
        $count = $category->spareparts()->withTrashed()->count();

        // Jika ternyata masih nyangkut, TOLAK permohonan hapus. Ini untuk mencegah error Constraint Violation / Orphan Data.
        if ($count > 0) {
            return response()->json([
                'message' => "Tidak dapat menghapus. Masih ada $count barang dalam kategori ini. Kosongkan terlebih dahulu.",
            ], 422);
        }

        $category->delete();
        Cache::forget('inventory_categories');
        Cache::forget('inventory_category_options');

        $this->logActivity('Kategori Dihapus', "Kategori '{$category->name}' dihapus.");

        return response()->json(['message' => 'Kategori berhasil dihapus.']);
    }
}
