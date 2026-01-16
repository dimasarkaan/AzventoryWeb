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
    public function index(Request $request)
    {
        $query = Sparepart::query();

        // Search Scope
        $query->when($request->search, function ($q) use ($request) {
            $q->where(function($sub) use ($request) {
                $sub->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('part_number', 'like', '%' . $request->search . '%')
                    ->orWhere('brand', 'like', '%' . $request->search . '%');
            });
        });

        // Filter Brand
        $query->when($request->brand && $request->brand !== 'Semua Merk', function ($q) use ($request) {
            $q->where('brand', $request->brand);
        });

        // Filter Category
        $query->when($request->category && $request->category !== 'Semua Kategori', function ($q) use ($request) {
            $q->where('category', $request->category);
        });

        // Filter Location
        $query->when($request->location && $request->location !== 'Semua Lokasi', function ($q) use ($request) {
            $q->where('location', $request->location);
        });

        // Filter Color
        $query->when($request->color && $request->color !== 'Semua Warna', function ($q) use ($request) {
            $q->where('color', $request->color);
        });

        // Sorting
        if ($request->sort) {
            switch ($request->sort) {
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'stock_asc':
                    $query->orderBy('stock', 'asc');
                    break;
                case 'stock_desc':
                    $query->orderBy('stock', 'desc');
                    break;
                 case 'price_asc':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('price', 'desc');
                    break;
                case 'oldest':
                    $query->oldest();
                    break;
                case 'newest':
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        // Get Data for Dropdowns
        $categories = Sparepart::select('category')->distinct()->pluck('category');
        $brands = Sparepart::whereNotNull('brand')->select('brand')->distinct()->pluck('brand');
        $locations = Sparepart::select('location')->distinct()->pluck('location');
        $colors = Sparepart::whereNotNull('color')->select('color')->distinct()->pluck('color');

        // Pagination with query string to keep filters
        $spareparts = $query->paginate(10);

        return view('superadmin.inventory.index', compact('spareparts', 'categories', 'locations', 'colors', 'brands'));
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
            'brand' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'condition' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'type' => 'required|in:sale,asset',
            'price' => 'required_if:type,sale|nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'status' => 'required|in:aktif,nonaktif',
            'image' => 'nullable|image|max:2048', // Max 2MB
        ]);

        $data = $request->all();

        // Handle Image Upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('spareparts', 'public');
        }

        $sparepart = Sparepart::create($data);

        // Generate and save QR Code
        $options = new \chillerlan\QRCode\QROptions([
            'outputBase64' => false,
        ]);
        $qrCodeUrl = route('superadmin.inventory.show', $sparepart);
        $qrCodeOutput = (new \chillerlan\QRCode\QRCode($options))->render($qrCodeUrl);
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
        $inventory->load(['borrowings' => function ($query) {
            $query->where('status', 'borrowed')->latest();
        }]);
        
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
            'brand' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'condition' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'type' => 'required|in:sale,asset',
            'price' => 'required_if:type,sale|nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:50',
            'status' => 'required|in:aktif,nonaktif',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        // Handle Image Upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($inventory->image && Storage::disk('public')->exists($inventory->image)) {
                Storage::disk('public')->delete($inventory->image);
            }
            $data['image'] = $request->file('image')->store('spareparts', 'public');
        }

        $inventory->update($data);

        // Check if part_number changed or QR code missing
        if ($inventory->wasChanged('part_number') || !$inventory->qr_code_path) {
             // Delete old QR if exists
            if ($inventory->getOriginal('qr_code_path') && Storage::disk('public')->exists($inventory->getOriginal('qr_code_path'))) {
                Storage::disk('public')->delete($inventory->getOriginal('qr_code_path'));
            }

            $options = new \chillerlan\QRCode\QROptions([
                'outputBase64' => false,
            ]);

            $qrCodeUrl = route('superadmin.inventory.show', $inventory);
            $qrCodeOutput = (new \chillerlan\QRCode\QRCode($options))->render($qrCodeUrl);
            $newQrCodePath = 'qrcodes/' . $inventory->part_number . '_' . $inventory->id . '.svg';
            Storage::disk('public')->put($newQrCodePath, $qrCodeOutput);
            
            $inventory->update(['qr_code_path' => $newQrCodePath]);
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
        if ($inventory->image && Storage::disk('public')->exists($inventory->image)) {
            Storage::disk('public')->delete($inventory->image);
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
