<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * @group Master Data
 *
 * API endpoints untuk kelola daftar lokasi atau ruangan laboratorium.
 */
class LocationController extends Controller
{
    use ActivityLogger;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::withCount('spareparts')->orderBy('name')->get()->map(function ($location) {
            return [
                'id' => $location->id,
                'name' => $location->name,
                'is_active' => (bool) $location->is_active,
                'is_default' => $location->is_default,
                'items_count' => $location->spareparts_count,
            ];
        });

        return response()->json($locations);
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role !== \App\Enums\UserRole::SUPERADMIN, 403, 'Hanya Superadmin yang dapat menambah lokasi baru.');

        $request->validate([
            'name' => 'required|string|max:191|unique:locations,name',
        ]);

        $location = Location::create(['name' => $request->name]);
        Cache::forget('inventory_locations');
        Cache::forget('inventory_location_options');

        $this->logActivity('Lokasi Dibuat', "Lokasi baru '{$location->name}' ditambahkan.");

        return response()->json([
            'message' => 'Lokasi baru berhasil ditambahkan.',
            'location' => $location,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => 'required|string|max:191|unique:locations,name,'.$location->id,
            'is_active' => 'sometimes|boolean',
        ]);

        $oldName = $location->name;
        $newName = $request->name;
        $oldActive = (bool) $location->is_active;
        $newActive = $request->has('is_active') ? (bool) $request->is_active : $oldActive;

        if ($location->is_default && ! $newActive) {
            return response()->json([
                'message' => 'Lokasi default tidak boleh dinonaktifkan.',
            ], 422);
        }

        $hasChanged = ($oldName !== $newName) || ($oldActive !== $newActive);

        DB::transaction(function () use ($location, $newName, $request) {
            // Update master table
            $updateData = ['name' => $newName];
            if ($request->has('is_active')) {
                $updateData['is_active'] = $request->is_active;
            }
            $location->update($updateData);
            // Tidak perlu bulk-update spareparts lagi karena relasi via FK (id tetap sama)
        });

        Cache::forget('inventory_locations');
        Cache::forget('inventory_location_options');

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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        if ($location->is_default) {
            return response()->json([
                'message' => 'Tidak dapat menghapus lokasi default. Harap jadikan lokasi lain sebagai default terlebih dahulu.',
            ], 422);
        }

        $count = $location->spareparts()->count();

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
