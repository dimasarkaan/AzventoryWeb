<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BorrowingController extends Controller
{
    /**
     * Store a new borrowing record (Pinjam Barang).
     */
    public function store(Request $request, Sparepart $sparepart)
    {
        $request->validate([
            'borrower_name' => 'required|string|max:255',
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
                'user_id' => auth()->id(), // Logic: Admin who processes the borrowing, or make a field for "Who is borrowing" if distinct from auth type
                'borrower_name' => $request->borrower_name,
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

        DB::transaction(function () use ($borrowing) {
            $borrowing->update([
                'status' => 'returned',
                'returned_at' => now(),
            ]);

            // Restore Stock
            $borrowing->sparepart->increment('stock', $borrowing->quantity);
        });

        return back()->with('success', 'Barang berhasil dikembalikan ke stok.');
    }
}
