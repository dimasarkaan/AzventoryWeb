<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Get list of inventory items.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Fetch spareparts with pagination
        // You can adjust pagination size or add filters here later
        $spareparts = Sparepart::orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'status' => 'success',
            'message' => 'Data inventory retrieved successfully',
            'data' => $spareparts
        ]);
    }

    /**
     * Store a new inventory item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'part_number' => 'required|unique:spareparts,part_number',
            'name' => 'required|string',
            'brand' => 'required|string',
            'location' => 'required|string',
            'type' => 'required|in:sale,asset',
            'stock' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string',
            'minimum_stock' => 'nullable|integer|min:0',
            'category' => 'required|string',
            'condition' => 'required|string',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $sparepart = Sparepart::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Sparepart created successfully',
            'data' => $sparepart
        ], 201);
    }

    /**
     * Get single inventory item detail.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $sparepart = Sparepart::find($id);

        if (!$sparepart) {
            return response()->json([
                'status' => 'error',
                'message' => 'Sparepart not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $sparepart
        ]);
    }

    /**
     * Update an inventory item.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $sparepart = Sparepart::find($id);

        if (!$sparepart) {
            return response()->json(['status' => 'error', 'message' => 'Sparepart not found'], 404);
        }

        $validated = $request->validate([
            'part_number' => 'sometimes|unique:spareparts,part_number,' . $id,
            'name' => 'sometimes|string',
            'brand' => 'sometimes|string',
            'location' => 'sometimes|string',
            'type' => 'sometimes|in:sale,asset',
            'stock' => 'sometimes|integer|min:0', // Use adjust-stock for logic based updates
            'price' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string',
            'minimum_stock' => 'nullable|integer|min:0',
            'category' => 'sometimes|string',
            'condition' => 'sometimes|string',
            'status' => 'sometimes|in:aktif,nonaktif',
        ]);

        $sparepart->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Sparepart updated successfully',
            'data' => $sparepart
        ]);
    }

    /**
     * Delete an inventory item.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $sparepart = Sparepart::find($id);

        if (!$sparepart) {
            return response()->json(['status' => 'error', 'message' => 'Sparepart not found'], 404);
        }

        $sparepart->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Sparepart deleted successfully'
        ]);
    }

    /**
     * Adjust stock (increment/decrement) for sales or supplies.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function adjustStock(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:increment,decrement',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string', // e.g. "Order #123"
        ]);

        $sparepart = Sparepart::find($id);

        if (!$sparepart) {
            return response()->json(['status' => 'error', 'message' => 'Sparepart not found'], 404);
        }

        if ($request->type === 'decrement' && $sparepart->stock < $request->quantity) {
            return response()->json(['status' => 'error', 'message' => 'Insufficient stock'], 400);
        }

        // Update Stock
        if ($request->type === 'increment') {
            $sparepart->increment('stock', $request->quantity);
        } else {
            $sparepart->decrement('stock', $request->quantity);
        }

        // Log the change
        // Assuming you have a StockLog model or similar. If not, we skip logging or just use activity log.
        // For now, allow basic Logging if model exists.
        if (class_exists(\App\Models\StockLog::class)) {
            \App\Models\StockLog::create([
                'sparepart_id' => $sparepart->id,
                'user_id' => null, // Sytem/API
                'type' => $request->type === 'increment' ? 'in' : 'out',
                'quantity' => $request->quantity,
                'description' => 'API Adjustment: ' . ($request->description ?? 'No description'),
                'date' => now(),
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Stock adjusted successfully',
            'data' => [
                'current_stock' => $sparepart->stock
            ]
        ]);
    }
}
