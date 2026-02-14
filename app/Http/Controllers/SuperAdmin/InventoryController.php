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
        $this->authorize('create', Sparepart::class);
        $options = $this->inventoryService->getDropdownOptions();
        return view('superadmin.inventory.create', $options);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\SuperAdmin\Inventory\StoreSparepartRequest $request)
    {
        $this->authorize('create', Sparepart::class);

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
            ->with(['user', 'returns'])
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
        $this->authorize('update', $inventory);
        $options = $this->inventoryService->getDropdownOptions();
        return view('superadmin.inventory.edit', array_merge(['sparepart' => $inventory], $options));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\SuperAdmin\Inventory\UpdateSparepartRequest $request, Sparepart $inventory)
    {
        $this->authorize('update', $inventory);

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
        $this->authorize('delete', $inventory);

        $result = $this->inventoryService->deleteSparepart($inventory);

        if ($result['status'] === 'error') {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('superadmin.inventory.index')
            ->with('success', $result['message']);
    }

    public function downloadQrCode(Sparepart $inventory)
    {
        $svgResponse = $this->qrCodeService->generateLabelSvg($inventory);

        $filename = $this->qrCodeService->getLabelFilename($inventory);


        return response($svgResponse, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function printQrCode(Sparepart $inventory)
    {
        if (!$inventory->qr_code_path) {
            abort(404, __('messages.qr_code_not_found'));
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
        $inventory = Sparepart::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $inventory);

        $result = $this->inventoryService->restoreSparepart($id);

        return redirect()->route('superadmin.inventory.index', ['trash' => 'true'])
            ->with('success', $result['message']);
    }

    /**
     * Permanently delete a sparepart.
     */
    public function forceDelete($id)
    {
        $inventory = Sparepart::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $inventory);

        $result = $this->inventoryService->forceDeleteSparepart($id);

        if ($result['status'] === 'error') {
            return redirect()->back()->with('error', $result['message']);
        }

        return redirect()->route('superadmin.inventory.index', ['trash' => 'true'])
            ->with('success', $result['message']);
    }

    /**
     * Permanently delete all spareparts in the trash.
     */
    public function forceDeleteAll()
    {
        $this->authorize('forceDelete', new Sparepart());
        $result = $this->inventoryService->forceDeleteAllSpareparts();

        return redirect()->route('superadmin.inventory.index', ['trash' => 'true'])
            ->with($result['status'] === 'empty' ? 'warning' : 'success', $result['message']);
    }

    /**
     * Bulk restore soft-deleted spareparts.
     */
    public function bulkRestore(Request $request)
    {
        $this->authorize('restore', new Sparepart());
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
         $this->authorize('forceDelete', new Sparepart());
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
