<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

// Controller khusus untuk mengelola Master Data Lokasi (Nama Gudang/Ruangan).
// Menangani fungsi CRUD (Tambah, Edit, Hapus) beserta aturan ketat khusus untuk lokasi utama (Default).
class LocationController extends Controller
{
    use ActivityLogger;

    // Menampilkan daftar seluruh lokasi yang ada, lengkap dengan jumlah barang yang tersimpan di dalamnya
    public function index()
    {
        // Menarik data dari database dan menghitung otomatis (withCount) relasi jumlah barangnya
        $locations = Location::withCount('spareparts')->orderBy('name')->get()->map(function ($location) {
            // Kita susun dalam bentuk Array agar mudah dibaca oleh Javascript di frontend
            return [
                'id' => $location->id,
                'name' => $location->name,
                'is_active' => (bool) $location->is_active,
                'is_default' => $location->is_default, // Penanda apakah ini gudang utama sistem
                'items_count' => $location->spareparts_count,
            ];
        });

        // Kembalikan ke browser sebagai format JSON API
        return response()->json($locations);
    }

    // Memproses pembuatan lokasi gudang baru
    public function store(Request $request)
    {
        // Pengecekan Keamanan Ekstra (Otorisasi): 
        // Hanya petinggi tertinggi (Superadmin) yang diizinkan membangun ruangan/gudang baru
        $this->authorize('create', Location::class);

        // Validasi: Nama harus ada dan tidak boleh kembar dengan lokasi yang sudah ada
        $request->validate([
            'name' => 'required|string|max:191|unique:locations,name',
        ]);

        $location = Location::create(['name' => $request->name]);
        
        // Hapus cache daftar lokasi lama agar formulir pendaftaran barang bisa melihat lokasi baru ini
        Cache::forget('inventory_locations');
        Cache::forget('inventory_location_options');

        // Tinggalkan jejak di buku audit trail
        $this->logActivity('Lokasi Dibuat', "Lokasi baru '{$location->name}' ditambahkan.");

        // Beri kode 201 (Created) ke Javascript sebagai tanda sukses
        return response()->json([
            'message' => 'Lokasi baru berhasil ditambahkan.',
            'location' => $location,
        ], 201);
    }

    // Menyimpan perubahan data lokasi (mengganti nama atau status aktifnya)
    public function update(Request $request, Location $location)
    {
        $this->authorize('update', $location);

        // Validasi: Nama tidak boleh sama dengan ruangan lain KECUALI namanya sendiri
        $request->validate([
            'name' => 'required|string|max:191|unique:locations,name,'.$location->id,
            'is_active' => 'sometimes|boolean',
        ]);

        // Tampung data lama sebagai bahan perbandingan
        $oldName = $location->name;
        $newName = $request->name;
        $oldActive = (bool) $location->is_active;
        $newActive = $request->has('is_active') ? (bool) $request->is_active : $oldActive;

        // Aturan Ketat Bisnis (Business Rule): 
        // Gudang utama (Default) yang dipakai operasional tidak boleh dinonaktifkan, bisa merusak sistem.
        if ($location->is_default && ! $newActive) {
            return response()->json([
                'message' => 'Lokasi default tidak boleh dinonaktifkan.',
            ], 422); // Kode 422: Ditolak karena melanggar aturan logika
        }

        // Cek apakah benar-benar ada data yang diganti oleh user
        $hasChanged = ($oldName !== $newName) || ($oldActive !== $newActive);

        // Bungkus penyimpanan ke dalam transaksi database demi keamanan
        DB::transaction(function () use ($location, $newName, $request) {
            $updateData = ['name' => $newName];
            if ($request->has('is_active')) {
                $updateData['is_active'] = $request->is_active;
            }
            $location->update($updateData);
            // Catatan: Tidak perlu update nama lokasi di semua barang, karena kita menggunakan Relasi Foreign Key (ID tetap sama)
        });

        // Buang cache lama
        Cache::forget('inventory_locations');
        Cache::forget('inventory_location_options');

        // Jika benar-benar ada perubahan data, catat detailnya
        if ($hasChanged) {
            $changes = [];
            if ($oldName !== $newName) {
                $changes['name'] = ['old' => $oldName, 'new' => $newName];
            }
            if ($oldActive !== $newActive) {
                $changes['is_active'] = ['old' => $oldActive, 'new' => $newActive];
            }

            // Tentukan deskripsi log yang pas sesuai apa saja yang diubah
            if ($oldName !== $newName && $oldActive !== $newActive) {
                $statusText = $newActive ? 'Aktif' : 'Non-aktif';
                $logMessage = "Nama lokasi diubah dari '{$oldName}' menjadi '{$newName}' dan status diubah menjadi {$statusText}.";
            } elseif ($oldName !== $newName) {
                $logMessage = "Nama lokasi diubah dari '{$oldName}' menjadi '{$newName}'.";
            } else {
                $statusText = $newActive ? 'Aktif' : 'Non-aktif';
                $logMessage = "Status lokasi '{$newName}' diubah menjadi {$statusText}.";
            }

            $this->logActivity('Lokasi Diperbarui', $logMessage, $changes);
        }

        return response()->json([
            'message' => 'Lokasi berhasil diperbarui.',
            'location' => $location,
        ]);
    }

    // Menghapus data lokasi/gudang dari sistem selamanya
    public function destroy(Location $location)
    {
        $this->authorize('delete', $location);

        // Aturan Ketat Bisnis: Gudang utama (Default) yang menjadi sandaran aplikasi tidak boleh dihapus sama sekali
        if ($location->is_default) {
            return response()->json([
                'message' => 'Tidak dapat menghapus lokasi default. Harap jadikan lokasi lain sebagai default terlebih dahulu.',
            ], 422);
        }

        // Pengecekan Keamanan: Jangan sampai menghapus gudang yang di dalamnya masih berisi barang fisik
        $count = $location->spareparts()->count();

        // Jika terdeteksi ada barang, tolak keras penghapusan untuk mencegah error Data Menggantung (Orphan Data)
        if ($count > 0) {
            return response()->json([
                'message' => "Tidak dapat menghapus. Masih ada {$count} barang di lokasi ini. Kosongkan terlebih dahulu.",
            ], 422);
        }

        $location->delete();
        Cache::forget('inventory_locations');
        Cache::forget('inventory_location_options');

        $this->logActivity('Lokasi Dihapus', "Lokasi '{$location->name}' dihapus.");

        return response()->json([
            'message' => 'Lokasi berhasil dihapus.',
        ]);
    }
}
