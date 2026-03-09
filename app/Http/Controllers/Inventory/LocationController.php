<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Sparepart;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class LocationController extends Controller
{
    use ActivityLogger;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::orderBy('name')->get()->map(function ($location) {
            return [
                'id' => $location->id,
                'name' => $location->name,
                'is_default' => $location->is_default,
                'items_count' => Sparepart::where('location', $location->name)->count(),
            ];
        });

        return response()->json($locations);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:191|unique:locations,name',
        ]);

        $location = Location::create(['name' => $request->name]);
        Cache::forget('inventory_locations');

        $this->logActivity('Lokasi Dibuat', "Lokasi baru '{$location->name}' ditambahkan.");

        return response()->json([
            'message' => 'Lokasi baru berhasil ditambahkan.',
            'location' => $location
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => 'required|string|max:191|unique:locations,name,' . $location->id,
        ]);

        $oldName = $location->name;
        $newName = $request->name;

        DB::transaction(function () use ($location, $oldName, $newName) {
            // Update master table
            $location->update(['name' => $newName]);

            // Bulk update spareparts table (the actual string)
            Sparepart::where('location', $oldName)->update(['location' => $newName]);
        });

        Cache::forget('inventory_locations');

        $this->logActivity('Lokasi Diperbarui', "Nama lokasi diubah dari '{$oldName}' menjadi '{$newName}'.");

        return response()->json([
            'message' => 'Lokasi berhasil diperbarui.',
            'location' => $location
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        $count = Sparepart::where('location', $location->name)->count();

        if ($count > 0) {
            return response()->json([
                'message' => "Tidak dapat menghapus. Masih ada {$count} barang di lokasi ini. Kosongkan terlebih dahulu."
            ], 422);
        }

        $location->delete();
        Cache::forget('inventory_locations');

        $this->logActivity('Lokasi Dihapus', "Lokasi '{$location->name}' dihapus.");

        return response()->json([
            'message' => 'Lokasi berhasil dihapus.'
        ]);
    }
}
