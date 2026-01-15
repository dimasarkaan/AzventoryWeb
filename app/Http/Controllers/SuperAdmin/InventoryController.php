<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use chillerlan\QRCode\QRCode;
use Illuminate\Support\Facades\Storage;
use App\Traits\ActivityLogger;

class InventoryController extends Controller
{
    use ActivityLogger;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $spareparts = Sparepart::latest()->paginate(10);
        return view('superadmin.inventory.index', compact('spareparts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('superadmin.inventory.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        App::setLocale('id');
        $request->validate([
            'name' => 'required|string|max:255',
            'part_number' => 'required|string|max:255|unique:spareparts',
            'category' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'condition' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $sparepart = Sparepart::create($request->all());

        // Generate and save QR Code
        $qrCodeUrl = route('superadmin.inventory.show', $sparepart);
        $qrCodeOutput = (new QRCode)->render($qrCodeUrl);
        $qrCodePath = 'qrcodes/' . $sparepart->part_number . '_' . $sparepart->id . '.svg';
        Storage::disk('public')->put($qrCodePath, $qrCodeOutput);

        $sparepart->update(['qr_code_path' => $qrCodePath]);

        $this->logActivity('Sparepart Dibuat', "Sparepart '{$sparepart->name}' (PN: {$sparepart->part_number}) telah ditambahkan.");

        return redirect()->route('superadmin.inventory.index')
            ->with('success', 'Sparepart berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sparepart $inventory)
    {
        return view('superadmin.inventory.show', ['sparepart' => $inventory]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sparepart $inventory)
    {
        return view('superadmin.inventory.edit', ['sparepart' => $inventory]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sparepart $inventory)
    {
        App::setLocale('id');
        $request->validate([
            'name' => 'required|string|max:255',
            'part_number' => 'required|string|max:255|unique:spareparts,part_number,' . $inventory->id,
            'category' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'condition' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $oldPartNumber = $inventory->part_number;
        $inventory->update($request->all());

        // Regenerate QR Code if part number has changed
        if ($oldPartNumber !== $request->part_number) {
            // Delete old QR Code if it exists
            if ($inventory->qr_code_path && Storage::disk('public')->exists($inventory->qr_code_path)) {
                Storage::disk('public')->delete($inventory->qr_code_path);
            }

            $qrCodeUrl = route('superadmin.inventory.show', $inventory);
            $qrCodeOutput = (new QRCode)->render($qrCodeUrl);
            $qrCodePath = 'qrcodes/' . $inventory->part_number . '_' . $inventory->id . '.svg';
            Storage::disk('public')->put($qrCodePath, $qrCodeOutput);
            $inventory->update(['qr_code_path' => $qrCodePath]);
        }

        $this->logActivity('Sparepart Diperbarui', "Data sparepart '{$inventory->name}' (PN: {$inventory->part_number}) telah diperbarui.");

        return redirect()->route('superadmin.inventory.index')
            ->with('success', 'Data sparepart berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sparepart $inventory)
    {
        // Also delete the QR code file from storage
        if ($inventory->qr_code_path && Storage::disk('public')->exists($inventory->qr_code_path)) {
            Storage::disk('public')->delete($inventory->qr_code_path);
        }

        $this->logActivity('Sparepart Dihapus', "Sparepart '{$inventory->name}' (PN: {$inventory->part_number}) telah dihapus.");

        $inventory->delete();

        return redirect()->route('superadmin.inventory.index')
            ->with('success', 'Data sparepart berhasil dihapus.');
    }

    public function downloadQrCode(Sparepart $inventory)
    {
        if (!$inventory->qr_code_path || !Storage::disk('public')->exists($inventory->qr_code_path)) {
            abort(404, 'QR Code not found.');
        }

        return Storage::disk('public')->download($inventory->qr_code_path);
    }

    public function printQrCode(Sparepart $inventory)
    {
        if (!$inventory->qr_code_path) {
            abort(404, 'QR Code not found.');
        }

        return view('superadmin.inventory.qr_print', ['sparepart' => $inventory]);
    }
}
