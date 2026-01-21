<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class BorrowingController extends Controller
{
    /**
     * Store a new borrowing record (Pinjam Barang).
     */
    public function store(Request $request, Sparepart $sparepart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'expected_return_at' => 'nullable|date|after:today',
        ]);

        if ($sparepart->stock < $request->quantity) {
             throw ValidationException::withMessages(['quantity' => 'Stok tidak mencukupi untuk peminjaman ini.']);
        }

        DB::transaction(function () use ($request, $sparepart) {
            // Create Borrowing Record
            Borrowing::create([
                'sparepart_id' => $sparepart->id,
                'user_id' => auth()->id(), 
                'borrower_name' => auth()->user()->name, // Auto-assigned to logged-in user
                'quantity' => $request->quantity,
                'borrowed_at' => now(),
                'expected_return_at' => $request->expected_return_at,
                'notes' => $request->notes,
                'status' => 'borrowed',
            ]);

            // Reduce Stock
            $sparepart->decrement('stock', $request->quantity);

            // Log Activity? (Optional but good)
        });

        return back()->with('success', 'Peminjaman berhasil dicatat.');
    }

    /**
     * Update borrowing status (Kembalikan Barang).
     */
    public function returnItem(Request $request, Borrowing $borrowing)
    {
        if ($borrowing->status !== 'borrowed') {
            return back()->with('error', 'Barang sudah dikembalikan.');
        }

        $request->validate([
            'return_quantity' => 'required|integer|min:1|max:' . $borrowing->quantity,
            'return_condition' => 'required|in:good,bad,lost',
            'return_notes' => 'nullable|string',
            'return_photos' => 'required|array|min:1|max:5',
            'return_photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $returnPhotos = [];
        if ($request->hasFile('return_photos')) {
             foreach ($request->file('return_photos') as $photo) {
                 $path = $photo->store('return_evidence', 'public');
                 $returnPhotos[] = $path;
             }
        }

        DB::transaction(function () use ($request, $borrowing, $returnPhotos) {
            $returnQty = $request->return_quantity;

            // PARTIAL RETURN LOGIC
            if ($returnQty < $borrowing->quantity) {
                // 1. Create NEW "Returned" Record using original data + return details
                Borrowing::create([
                    'sparepart_id' => $borrowing->sparepart_id,
                    'user_id' => $borrowing->user_id,
                    'borrower_name' => $borrowing->borrower_name,
                    'quantity' => $returnQty, // The returned amount
                    'borrowed_at' => $borrowing->borrowed_at,
                    'expected_return_at' => $borrowing->expected_return_at,
                    'returned_at' => now(),
                    'notes' => $borrowing->notes,
                    'status' => 'returned',
                    'return_condition' => $request->return_condition,
                    'return_notes' => $request->return_notes,
                    'return_photos' => $returnPhotos, // Auto-casted to array by model
                ]);

                // 2. Decrement ORIGINAL Record quantity
                $borrowing->decrement('quantity', $returnQty);
                // Status remains 'borrowed' (or 'active') for the remaining items

            } else {
                // FULL RETURN LOGIC (Existing)
                $borrowing->update([
                    'status' => 'returned',
                    'returned_at' => now(),
                    'return_condition' => $request->return_condition,
                    'return_notes' => $request->return_notes,
                    'return_photos' => $returnPhotos,
                ]);
            }

            // Restore Stock ONLY if condition is 'good'
            if ($request->return_condition === 'good') {
                $borrowing->sparepart->increment('stock', $returnQty);
                // Log Stock In
                \App\Models\StockLog::create([
                    'sparepart_id' => $borrowing->sparepart_id,
                    'user_id' => auth()->id(),
                    'type' => 'masuk',
                    'quantity' => $returnQty,
                    'reason' => 'Pengembalian Peminjaman (Kondisi Baik) oleh ' . $borrowing->borrower_name,
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);
            } else {
                // Log Activity for Bad/Lost item
                // Ideally creating a "Damaged Item" log or similar, for now just activity log
                // Or logging stock movement but not adding to main stock? 
                // Decision: Do NOT add to stock. Just log the incident.
            }
        });

        $message = "Barang berhasil dikembalikan.";
        if ($request->return_condition !== 'good') {
            $message .= " Stok tidak dikembalikan karena kondisi " . ($request->return_condition === 'bad' ? 'Rusak' : 'Hilang') . ".";
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => $message]);
        }

        return back()->with('success', $message);
    }
}
