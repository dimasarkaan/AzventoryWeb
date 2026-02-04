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
    protected $qrCodeService;

    public function __construct(InventoryService $inventoryService, \App\Services\QrCodeService $qrCodeService)
    {
        $this->inventoryService = $inventoryService;
        $this->qrCodeService = $qrCodeService;
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
        $options = $this->inventoryService->getDropdownOptions();
        return view('superadmin.inventory.create', $options);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\SuperAdmin\Inventory\StoreSparepartRequest $request)
    {
        $result = $this->inventoryService->createSparepart($request->validated());

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
        $borrowings = $inventory->borrowings()
            ->with('user')
            ->latest()
            ->paginate(5, ['*'], 'history_page');

        // Fetch similar items with pagination (3 per page) 
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
        $options = $this->inventoryService->getDropdownOptions();
        return view('superadmin.inventory.edit', array_merge(['sparepart' => $inventory], $options));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\SuperAdmin\Inventory\UpdateSparepartRequest $request, Sparepart $inventory)
    {
        $result = $this->inventoryService->updateSparepart($inventory, $request->validated());

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
        $svgResponse = $this->qrCodeService->generateLabelSvg($inventory);

        // Generate structured filename: Label-[Category]-[Brand]-[PartNumber].svg
        $cat = Str::title($inventory->category);
        $brand = Str::title($inventory->brand);
        $pn = strtoupper($inventory->part_number);
        
        $catSlug = preg_replace('/[^A-Za-z0-9\-]/', '-', $cat);
        $brandSlug = preg_replace('/[^A-Za-z0-9\-]/', '-', $brand);
        $pnSlug = preg_replace('/[^A-Za-z0-9\-]/', '-', $pn);

        $filename = "Label-{$catSlug}-{$brandSlug}-{$pnSlug}.svg";

        return response($svgResponse, 200, [
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
                    'image_path' => $sparepart->image,
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
        $result = $this->inventoryService->restoreSparepart($id);

        return redirect()->route('superadmin.inventory.index', ['trash' => 'true'])
            ->with('success', $result['message']);
    }

    /**
     * Permanently delete a sparepart.
     */
    public function forceDelete($id)
    {
        $result = $this->inventoryService->forceDeleteSparepart($id);

        return redirect()->route('superadmin.inventory.index', ['trash' => 'true'])
            ->with('success', $result['message']);
    }

    /**
     * Permanently delete all spareparts in the trash.
     */
    public function forceDeleteAll()
    {
        $result = $this->inventoryService->forceDeleteAllSpareparts();

        return redirect()->route('superadmin.inventory.index', ['trash' => 'true'])
            ->with($result['status'] === 'empty' ? 'warning' : 'success', $result['message']);
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

        $result = $this->inventoryService->bulkRestore($request->ids);

        if ($result['status'] === 'empty') {
             return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', $result['message']);
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

        $result = $this->inventoryService->bulkForceDelete($request->ids);

        if ($result['status'] === 'empty') {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->back()->with('success', $result['message']);
    }
}
