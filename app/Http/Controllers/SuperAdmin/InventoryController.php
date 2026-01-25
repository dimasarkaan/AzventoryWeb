<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use chillerlan\QRCode\QRCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

        // Filter Low Stock
        if ($request->filter === 'low_stock') {
            $query->whereColumn('stock', '<=', 'minimum_stock');
        }

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
            'part_number' => 'required|string|max:255',
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
        } elseif ($request->filled('existing_image')) {
            // Copy existing image to a new file to avoid deletion issues/sharing constraint
            $existingPath = $request->existing_image;
            if (Storage::disk('public')->exists($existingPath)) {
                $extension = pathinfo($existingPath, PATHINFO_EXTENSION);
                $newPath = 'spareparts/' . \Illuminate\Support\Str::random(40) . '.' . $extension;
                Storage::disk('public')->copy($existingPath, $newPath);
                $data['image'] = $newPath;
            }
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

        // Log Initial Stock if > 0
        if ($sparepart->stock > 0) {
            \App\Models\StockLog::create([
                'sparepart_id' => $sparepart->id,
                'user_id' => auth()->id(),
                'type' => 'masuk',
                'quantity' => $sparepart->stock,
                'reason' => 'Stok awal (Item baru)',
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        }

        $this->logActivity('Sparepart Dibuat', "Sparepart '{$sparepart->name}' (PN: {$sparepart->part_number}) telah ditambahkan.");

        return redirect()->route('superadmin.inventory.index')
            ->with('success', 'Sparepart berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sparepart $inventory)
    {
        $inventory->load(['borrowings.user' => function ($query) {
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
            'part_number' => 'required|string|max:255',
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

        // Check for Low Stock Notification
        if ($inventory->minimum_stock > 0 && $inventory->stock <= $inventory->minimum_stock && $inventory->wasChanged('stock')) {
            $admins = \App\Models\User::whereIn('role', ['superadmin', 'admin'])->get();
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\LowStockNotification($inventory));
        }

        $this->logActivity('Sparepart Diperbarui', "Data sparepart '{$inventory->name}' (PN: {$inventory->part_number}) telah diperbarui.");

        return redirect()->route('superadmin.inventory.index')
            ->with('success', 'Data sparepart berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $inventory = Sparepart::findOrFail($id);

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
            abort(404, 'QR Code tidak ditemukan.');
        }

        // 1. Read existing QR Code SVG
        $qrContent = Storage::disk('public')->get($inventory->qr_code_path);
        
        // Extract the <path> or <g> content from the existing SVG
        // Simple regex to grab everything between <svg ...> and </svg>
        preg_match('/<svg[^>]*>(.*?)<\/svg>/s', $qrContent, $matches);
        $qrInnerContent = $matches[1] ?? '';

        // 2. Define Dimensions (33mm x 15mm @ 96DPI)
        // 1mm = 3.7795 px
        // Width: 33mm * 3.7795 ≈ 125px
        // Height: 15mm * 3.7795 ≈ 57px
        $width = 125;
        $height = 57;
        
        // QR Size: 12mm * 3.7795 ≈ 45px
        $qrSize = 45;
        $qrMargin = ($height - $qrSize) / 2; // Center vertically (~6px)

        // 3. Construct New SVG
        $svg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="33mm" height="15mm" viewBox="0 0 ' . $width . ' ' . $height . '" version="1.1" xmlns="http://www.w3.org/2000/svg">
    <rect width="100%" height="100%" fill="white"/>
    
    <!-- QR Code Section (Left) -->
    <g transform="translate(' . $qrMargin . ', ' . $qrMargin . ') scale(' . ($qrSize / 53) . ')">
        <!-- Scale factor depends on original QR viewBox size. Chillerlan usually defaults to ~53x53 blocks for V5? 
             Actually, simpler approach: Use an <image> tag with base64 if path extraction is tricky, 
             OR just put the path in a container with defined width/height. 
             Let`s try wrapping the inner content in an SVG element to handle scaling automatically. 
        -->
        <svg width="53" height="53" viewBox="0 0 53 53">
            ' . $qrInnerContent . '
        </svg>
    </g>

    <!-- Text Section (Right) -->
    <g font-family="monospace" fill="black">
        <!-- Part Number Header (Small) -->
        <text x="55" y="15" font-size="6" font-weight="bold" fill="#444">PART NUMBER</text>
        
        <!-- Part Number (Large) -->
        <text x="55" y="28" font-size="8" font-weight="bold">' . htmlspecialchars($inventory->part_number) . '</text>
        
        <!-- Name (Truncated) -->
        <text x="55" y="42" font-size="6">' . htmlspecialchars(Str::limit($inventory->name, 18)) . '</text>
    </g>
</svg>';
        
        // Only for specific QR generator versions:
        // If the original QR SVG relied on a specific viewBox, we might need to adjust the `scale` or nested `svg` viewBox.
        // Chillerlan V5 standard: check the original file content? 
        // Safer way: regeneraet QR code object to get the matrix size, OR assume standard.
        // Let's rely on the nested <svg> trick with viewBox="0 0 X Y".
        // Use a known Chillerlan option "eccLevel" etc? 
        // For robustness, Re-generate the QR with known options is safer than regex parsing:
        
        $options = new \chillerlan\QRCode\QROptions([
            'outputBase64' => false,
            'imageTransparent' => false, // We want white background? No, transparent path on white rect.
        ]);
        // Re-render purely to get a clean string we control
        $freshQr = (new \chillerlan\QRCode\QRCode($options))->render(route('superadmin.inventory.show', $inventory));
         
        // Extract viewBox from fresh QR
        preg_match('/viewBox="([^"]+)"/', $freshQr, $vbMatches);
        $qrViewBox = $vbMatches[1] ?? '0 0 53 53'; // Fallback
        
        preg_match('/<svg[^>]*>(.*?)<\/svg>/s', $freshQr, $contentMatches);
        $cleanInner = $contentMatches[1] ?? '';

        $finalSvg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="33mm" height="15mm" viewBox="0 0 ' . $width . ' ' . $height . '" version="1.1" xmlns="http://www.w3.org/2000/svg">
    <!-- Background with Border Stroke -->
    <rect x="0.5" y="0.5" width="' . ($width - 1) . '" height="' . ($height - 1) . '" fill="white" stroke="black" stroke-width="0.5" rx="3" ry="3"/>
    
    <!-- QR Code (Left) -->
    <!-- We constrain it to 45x45 pixels -->
    <svg x="' . $qrMargin . '" y="' . $qrMargin . '" width="' . $qrSize . '" height="' . $qrSize . '" viewBox="' . $qrViewBox . '">
        ' . $cleanInner . '
    </svg>

    <!-- Text (Right) -->
    <g font-family="sans-serif" fill="black">
        <text x="55" y="18" font-size="5" font-weight="bold" fill="#555">PART NUMBER</text>
        <text x="55" y="29" font-size="8" font-family="monospace" font-weight="bold">' . htmlspecialchars($inventory->part_number) . '</text>
        <text x="55" y="40" font-size="6">' . htmlspecialchars(\Illuminate\Support\Str::limit($inventory->name, 20)) . '</text>
    </g>
</svg>';

        return response($finalSvg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="Label-' . $inventory->part_number . '.svg"',
        ]);
    }

    public function printQrCode(Sparepart $inventory)
    {
        if (!$inventory->qr_code_path) {
            abort(404, 'QR Code tidak ditemukan.');
        }

        return view('superadmin.inventory.print_label', ['sparepart' => $inventory]);
    }
    public function checkPartNumber(Request $request)
    {
        $partNumber = $request->query('part_number');
        $sparepart = Sparepart::where('part_number', $partNumber)->first();

        if ($sparepart) {
            return response()->json([
                'exists' => true,
                'data' => [
                    'name' => $sparepart->name,
                    'brand' => $sparepart->brand,
                    'category' => $sparepart->category,
                    'type' => $sparepart->type,
                    'unit' => $sparepart->unit,
                    'price' => $sparepart->price,
                    'image_url' => $sparepart->image ? Storage::url($sparepart->image) : null,
                    'image_path' => $sparepart->image, // Return raw path for backend copying logic if needed
                ]
            ]);
        }

        return response()->json(['exists' => false]);
    }
}
