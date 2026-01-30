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
use App\Services\InventoryService;

class InventoryController extends Controller
{
    use ActivityLogger;

    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $spareparts = $this->inventoryService->getFilteredSpareparts($request->all(), 10);
        $options = $this->inventoryService->getDropdownOptions();

        return view('superadmin.inventory.index', array_merge([
            'spareparts' => $spareparts,
        ], $options));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = \Illuminate\Support\Facades\Cache::remember('inventory_categories', 3600, function () {
            return Sparepart::select('category')->distinct()->pluck('category');
        });

        $brands = \Illuminate\Support\Facades\Cache::remember('inventory_brands', 3600, function () {
            return Sparepart::whereNotNull('brand')->select('brand')->distinct()->pluck('brand');
        });

        $colors = \Illuminate\Support\Facades\Cache::remember('inventory_colors', 3600, function () {
            return Sparepart::whereNotNull('color')->select('color')->distinct()->pluck('color');
        });

        $units = \Illuminate\Support\Facades\Cache::remember('inventory_units', 3600, function () {
            return Sparepart::whereNotNull('unit')->select('unit')->distinct()->pluck('unit');
        });

        $names = \Illuminate\Support\Facades\Cache::remember('inventory_names', 3600, function () {
            return Sparepart::select('name')->distinct()->pluck('name');
        });

        $partNumbers = \Illuminate\Support\Facades\Cache::remember('inventory_part_numbers', 3600, function () {
            return Sparepart::select('part_number')->distinct()->pluck('part_number');
        });

        return view('superadmin.inventory.create', compact('categories', 'brands', 'colors', 'units', 'names', 'partNumbers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        App::setLocale('id');

        $validated = $request->validate([
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

        // Use ONLY validated data
        $data = $validated;

        // Check for exact duplicate (Same PN + all other physical attributes)
        $existingItemQuery = Sparepart::where('part_number', $data['part_number'])
            ->where('name', $data['name'])
            ->where('brand', $data['brand'])
            ->where('category', $data['category'])
            ->where('location', $data['location'])
            ->where('condition', $data['condition'])
            ->where('type', $data['type']);

        // Handle nullable fields for duplicate check
        foreach (['color', 'price', 'unit'] as $field) {
            if (isset($data[$field])) {
                $existingItemQuery->where($field, $data[$field]);
            } else {
                $existingItemQuery->whereNull($field);
            }
        }

        $existingItem = $existingItemQuery->first();

        if ($existingItem) {
            // DUPLICATE FOUND: Merge Stock
            if ($data['stock'] > 0) {
                $existingItem->stock += $data['stock'];
                $existingItem->save();
                
                $sparepart = $existingItem;
                $message = "Stok sparepart '{$sparepart->name}' (PN: {$sparepart->part_number}) berhasil ditambahkan ke item yang sudah ada.";

                // Log Stock Addition
                \App\Models\StockLog::create([
                    'sparepart_id' => $sparepart->id,
                    'user_id' => auth()->id(),
                    'type' => 'masuk',
                    'quantity' => $data['stock'],
                    'reason' => 'Penambahan stok (Duplicate Entry)',
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);

                $this->logActivity('Stok Diupdate', $message);
                
                return redirect()->route('superadmin.inventory.index')->with('success', $message);
            } else {
                // Duplicate input but 0 stock - No Action
                $message = "Item '{$existingItem->name}' (PN: {$existingItem->part_number}) sudah ada di inventaris dan stok input adalah 0. Silakan periksa kembali jumlah stok.";
                return redirect()->back()->withInput()->with('warning', $message); // Stay on page, keep input
            }
            
            // Note: We intentionally ignore image upload for duplicates to preserve existing item consistency
            
        } else {
            // NEW ITEM: Handle Image & Create

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
            
            $message = "Sparepart '{$sparepart->name}' (PN: {$sparepart->part_number}) telah ditambahkan.";
            $this->logActivity('Sparepart Dibuat', $message);
        }

        // Clear Cache to update filters
        $this->clearFilterCache();

        return redirect()->route('superadmin.inventory.index')
            ->with('success', $message);
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
        $categories = \Illuminate\Support\Facades\Cache::remember('inventory_categories', 3600, function () {
            return Sparepart::select('category')->distinct()->pluck('category');
        });

        $brands = \Illuminate\Support\Facades\Cache::remember('inventory_brands', 3600, function () {
            return Sparepart::whereNotNull('brand')->select('brand')->distinct()->pluck('brand');
        });

        $colors = \Illuminate\Support\Facades\Cache::remember('inventory_colors', 3600, function () {
            return Sparepart::whereNotNull('color')->select('color')->distinct()->pluck('color');
        });

        $units = \Illuminate\Support\Facades\Cache::remember('inventory_units', 3600, function () {
            return Sparepart::whereNotNull('unit')->select('unit')->distinct()->pluck('unit');
        });

        $names = \Illuminate\Support\Facades\Cache::remember('inventory_names', 3600, function () {
            return Sparepart::select('name')->distinct()->pluck('name');
        });

        $partNumbers = \Illuminate\Support\Facades\Cache::remember('inventory_part_numbers', 3600, function () {
            return Sparepart::select('part_number')->distinct()->pluck('part_number');
        });

        return view('superadmin.inventory.edit', ['sparepart' => $inventory, 'categories' => $categories, 'brands' => $brands, 'colors' => $colors, 'units' => $units, 'names' => $names, 'partNumbers' => $partNumbers]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sparepart $inventory)
    {
        App::setLocale('id');
        $validated = $request->validate([
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

        $data = $validated;

        // Handle Image Upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($inventory->image && Storage::disk('public')->exists($inventory->image)) {
                Storage::disk('public')->delete($inventory->image);
            }
            $data['image'] = $request->file('image')->store('spareparts', 'public');
        }

        $inventory->fill($data);

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

        // Clear Cache to update filters
        // Calculate changes for logging
        $changes = [];
        if ($inventory->isDirty()) {
            foreach ($inventory->getDirty() as $key => $value) {
                $original = $inventory->getOriginal($key);
                $changes[$key] = [
                    'old' => $original,
                    'new' => $value,
                ];
            }
        }

        $inventory->save();

        // Clear filter cache
        $this->clearFilterCache();

        $this->logActivity('Sparepart Diperbarui', "Data sparepart '{$inventory->name}' (PN: {$inventory->part_number}) telah diperbarui.", $changes);

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

    /**
     * Clear the inventory filter caches.
     */
    private function clearFilterCache()
    {
        \Illuminate\Support\Facades\Cache::forget('inventory_categories');
        \Illuminate\Support\Facades\Cache::forget('inventory_brands');
        \Illuminate\Support\Facades\Cache::forget('inventory_locations');
        \Illuminate\Support\Facades\Cache::forget('inventory_locations');
        \Illuminate\Support\Facades\Cache::forget('inventory_colors');
        \Illuminate\Support\Facades\Cache::forget('inventory_units');
    }
}
