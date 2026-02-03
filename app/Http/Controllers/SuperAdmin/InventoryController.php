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
            'existing_image' => 'nullable|string',
        ]);

        $result = $this->inventoryService->createSparepart($validated);

        if ($result['status'] === 'error_zero_stock') {
            return redirect()->back()->withInput()->with('warning', $result['message']);
        }

        return redirect()->route('superadmin.inventory.index')->with('success', $result['message']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sparepart $inventory)
    {
        // Fetch borrowings with pagination (5 per page)
        // Using 'history_page' as the query parameter so it doesn't conflict with similar items
        $borrowings = $inventory->borrowings()
            ->with('user')
            ->latest()
            ->paginate(5, ['*'], 'history_page');

        // Fetch similar items with pagination (3 per page) 
        // Using 'similar_page' as the query parameter
        $similarItems = Sparepart::where('part_number', $inventory->part_number)
            ->where('id', '!=', $inventory->id)
            ->paginate(3, ['*'], 'similar_page');

        return view('superadmin.inventory.show', [
            'sparepart' => $inventory,
            'similarItems' => $similarItems,
            'borrowings' => $borrowings
        ]);
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

        $result = $this->inventoryService->updateSparepart($inventory, $validated);

        return redirect()->route('superadmin.inventory.index')
            ->with('success', $result['message']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $inventory = Sparepart::findOrFail($id);
        $result = $this->inventoryService->deleteSparepart($inventory);

        return redirect()->route('superadmin.inventory.index')
            ->with('success', $result['message']);
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

        // Generate structured filename: Label-[Category]-[Brand]-[PartNumber].svg
        // Format: Label-Category-Brand-PARTNUMBER (Title Case for text, Upper for PN)
        $cat = \Illuminate\Support\Str::title($inventory->category);
        $brand = \Illuminate\Support\Str::title($inventory->brand);
        $pn = strtoupper($inventory->part_number);
        
        // Sanitize: Replace spaces with hyphens, remove special chars
        $catSlug = preg_replace('/[^A-Za-z0-9\-]/', '-', $cat);
        $brandSlug = preg_replace('/[^A-Za-z0-9\-]/', '-', $brand);
        $pnSlug = preg_replace('/[^A-Za-z0-9\-]/', '-', $pn);

        $filename = "Label-{$catSlug}-{$brandSlug}-{$pnSlug}.svg";

        return response($finalSvg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
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
     * Restore a soft-deleted sparepart.
     */
    public function restore($id)
    {
        $sparepart = Sparepart::onlyTrashed()->findOrFail($id);
        $sparepart->restore();
        
        // Log activity
        $this->logActivity('Sparepart Dipulihkan', "Sparepart '{$sparepart->name}' (PN: {$sparepart->part_number}) telah dipulihkan dari tong sampah.");
        // Clear cache
        $this->inventoryService->clearCache();

        return redirect()->route('superadmin.inventory.index', ['trash' => 'true'])
            ->with('success', 'Data sparepart berhasil dipulihkan.');
    }

    /**
     * Permanently delete a sparepart.
     */
    public function forceDelete($id)
    {
        $sparepart = Sparepart::onlyTrashed()->findOrFail($id);
        
        // Delete associated files
        if ($sparepart->qr_code_path && Storage::disk('public')->exists($sparepart->qr_code_path)) {
            Storage::disk('public')->delete($sparepart->qr_code_path);
        }
        if ($sparepart->image && Storage::disk('public')->exists($sparepart->image)) {
            Storage::disk('public')->delete($sparepart->image);
        }

        $sparepart->forceDelete();
        
        // Log activity
        $this->logActivity('Sparepart Dihapus Permanen', "Sparepart '{$sparepart->name}' (PN: {$sparepart->part_number}) telah dihapus permanen.");
        // Clear cache
        $this->inventoryService->clearCache();

        return redirect()->route('superadmin.inventory.index', ['trash' => 'true'])
            ->with('success', 'Data sparepart berhasil dihapus permanen.');
    }

    /**
     * Permanently delete all spareparts in the trash.
     */
    public function forceDeleteAll()
    {
        $spareparts = Sparepart::onlyTrashed()->get();

        if ($spareparts->isEmpty()) {
            return redirect()->route('superadmin.inventory.index', ['trash' => 'true'])
                ->with('warning', 'Tempat sampah sudah kosong.');
        }

        foreach ($spareparts as $sparepart) {
            // Delete associated files
            if ($sparepart->qr_code_path && Storage::disk('public')->exists($sparepart->qr_code_path)) {
                Storage::disk('public')->delete($sparepart->qr_code_path);
            }
            if ($sparepart->image && Storage::disk('public')->exists($sparepart->image)) {
                Storage::disk('public')->delete($sparepart->image);
            }
            
            $sparepart->forceDelete();
        }

        // Log activity
        $this->logActivity('Tong Sampah Dikosongkan', "Semua item di tong sampah (" . $spareparts->count() . " item) telah dihapus permanen.");
        // Clear cache
        $this->inventoryService->clearCache();

        return redirect()->route('superadmin.inventory.index', ['trash' => 'true'])
            ->with('success', 'Semua data di tong sampah berhasil dihapus permanen.');
    }

    /**
     * Bulk restore soft-deleted spareparts.
     */
    public function bulkRestore(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:spareparts,id',
        ]);

        $ids = $request->ids;
        $count = Sparepart::onlyTrashed()->whereIn('id', $ids)->count();
        
        if ($count === 0) {
             return redirect()->back()->with('error', 'Tidak ada item yang dipilih untuk dipulihkan.');
        }

        Sparepart::onlyTrashed()->whereIn('id', $ids)->restore();

        // Log activity
        $this->logActivity('Bulk Restore', "$count item berhasil dipulihkan dari tong sampah.");
        $this->inventoryService->clearCache();

        return redirect()->back()->with('success', "$count item berhasil dipulihkan.");
    }

    /**
     * Bulk permanently delete spareparts.
     */
    public function bulkForceDelete(Request $request)
    {
         $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:spareparts,id',
        ]);

        $ids = $request->ids;
        $spareparts = Sparepart::onlyTrashed()->whereIn('id', $ids)->get();

        if ($spareparts->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada item yang dipilih untuk dihapus.');
        }

        $count = $spareparts->count();

        foreach ($spareparts as $sparepart) {
            // Delete associated files
            if ($sparepart->qr_code_path && Storage::disk('public')->exists($sparepart->qr_code_path)) {
                Storage::disk('public')->delete($sparepart->qr_code_path);
            }
            if ($sparepart->image && Storage::disk('public')->exists($sparepart->image)) {
                Storage::disk('public')->delete($sparepart->image);
            }
            
            $sparepart->forceDelete();
        }

        // Log activity
        $this->logActivity('Bulk Force Delete', "$count item telah dihapus permanen dari tong sampah.");
        $this->inventoryService->clearCache();

        return redirect()->back()->with('success', "$count item berhasil dihapus permanen.");
    }
}
