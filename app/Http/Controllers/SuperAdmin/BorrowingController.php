<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Traits\ActivityLogger;

class BorrowingController extends Controller
{
    use ActivityLogger;
    protected $imageOptimizer;

    public function __construct(\App\Services\ImageOptimizationService $imageOptimizer)
    {
        $this->imageOptimizer = $imageOptimizer;
    }

    /**
     * Display borrowing detail with returns history.
     */
    public function show(Borrowing $borrowing)
    {
        $borrowing->load(['user', 'sparepart', 'returns' => function($query) {
            $query->orderBy('return_date', 'desc');
        }]);

        return view('superadmin.inventory.borrow.show', [
            'borrowing' => $borrowing
        ]);
    }

    /**
     * Store a new borrowing record (Pinjam Barang).
     */
    public function store(Request $request, Sparepart $sparepart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'expected_return_at' => 'required|date|after:today',
        ]);

        if ($sparepart->stock < $request->quantity) {
             throw ValidationException::withMessages(['quantity' => 'Stok tidak mencukupi untuk peminjaman ini.']);
        }

        if ($sparepart->condition !== 'Baik') {
            throw ValidationException::withMessages(['condition' => 'Hanya barang dengan kondisi "Baik" yang dapat dipinjam.']);
        }

        DB::transaction(function () use ($request, $sparepart) {
            // Create Borrowing Record
            Borrowing::create([
                'sparepart_id' => $sparepart->id,
                'user_id' => auth()->id(), 
                'borrower_name' => auth()->user()->name, 
                'quantity' => $request->quantity,
                'borrowed_at' => now(),
                'expected_return_at' => $request->expected_return_at,
                'notes' => $request->notes,
                'status' => 'borrowed',
            ]);

            // Reduce Stock
            $sparepart->decrement('stock', $request->quantity);

            // Log Activity
            $this->logActivity('Peminjaman Barang', "Meminjam {$request->quantity} {$sparepart->unit} '{$sparepart->name}'. Catatan: " . ($request->notes ?? '-'));
        });

        return redirect()->route('superadmin.inventory.show', $sparepart->id)
            ->with('success', 'Peminjaman berhasil dicatat.');
    }

    /**
     * Update borrowing status (Kembalikan Barang).
     * REWRITTEN LOGIC: Explicit summation and strict status updates.
     */
    public function returnItem(Request $request, Borrowing $borrowing)
    {
        // Authorization Check: Start
        // Allow if user is the borrower OR if user has elevated privileges (SuperAdmin, Admin, Operator)
        $user = auth()->user();
        $isOwner = $borrowing->user_id === $user->id;
        $hasPrivilege = in_array($user->role, [
            \App\Enums\UserRole::SUPERADMIN, 
            \App\Enums\UserRole::ADMIN, 
            \App\Enums\UserRole::OPERATOR
        ]);

        if (!$isOwner && !$hasPrivilege) {
            abort(403, 'Anda tidak memiliki izin untuk mengembalikan barang ini.');
        }
        // Authorization Check: End
        // 1. Calculate Current State directly from DB
        $totalReturned = $borrowing->returns()->sum('quantity');
        $remaining = $borrowing->quantity - $totalReturned;

        if ($remaining <= 0) {
            return back()->with('error', 'Semua barang sudah dikembalikan.');
        }

        $request->validate([
            'return_quantity' => 'required|integer|min:1|max:' . $remaining,
            'return_condition' => 'required|in:good,bad,lost',
            'return_notes' => 'nullable|string',
            'return_photos' => [
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->return_condition !== 'lost' && empty($value)) {
                        $fail('Bukti foto wajib diunggah untuk kondisi Barang Baik atau Rusak.');
                    }
                },
                'array',
                'min:1',
                'max:5'
            ],
            'return_photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB
        ], [
            'return_quantity.required' => 'Jumlah wajib diisi.',
            'return_quantity.integer' => 'Jumlah harus berupa angka.',
            'return_quantity.min' => 'Jumlah minimal 1.',
            'return_quantity.max' => 'Jumlah tidak boleh melebihi sisa pinjaman (:max).',
            'return_condition.required' => 'Kondisi barang wajib dipilih.',
            'return_condition.in' => 'Kondisi tidak valid.',
            'return_photos.array' => 'Format foto tidak valid.',
            'return_photos.min' => 'Minimal unggah :min foto.',
            'return_photos.max' => 'Maksimal unggah :max foto.',
            'return_photos.*.image' => 'File harus berupa gambar.',
            'return_photos.*.mimes' => 'Format file harus jpeg, png, jpg, atau gif.',
            'return_photos.*.max' => 'Ukuran file maksimal 5MB.',
        ]);

        $returnPhotos = [];
        if ($request->hasFile('return_photos')) {
             foreach ($request->file('return_photos') as $photo) {
                 // Use ImageOptimizationService
                 $returnPhotos[] = $this->imageOptimizer->optimizeAndSave($photo, 'return_evidence');
             }
        }

        DB::transaction(function () use ($request, $borrowing, $returnPhotos) {
            $qty = $request->return_quantity;
            $condition = $request->return_condition;

            // 2. Create Return Record
            $borrowing->returns()->create([
                'return_date' => now(),
                'quantity' => $qty,
                'condition' => $condition,
                'notes' => $request->return_notes,
                'photos' => $returnPhotos, 
            ]);

            // 3. Update Parent Status
            // Re-calculate total returned including the new record
            $newTotalReturned = $borrowing->returns()->sum('quantity');
            
            if ($newTotalReturned >= $borrowing->quantity) {
                $borrowing->update([
                    'status' => 'returned',
                    'returned_at' => now(),
                ]);
            }

            // 4. Inventory Adjustment
            $originalSparepart = $borrowing->sparepart;

            if ($condition === 'good') {
                $originalSparepart->increment('stock', $qty);
                
                \App\Models\StockLog::create([
                    'sparepart_id' => $originalSparepart->id,
                    'user_id' => auth()->id(),
                    'type' => 'masuk',
                    'quantity' => $qty,
                    'reason' => 'Pengembalian Peminjaman (Kondisi Baik) oleh ' . $borrowing->borrower_name,
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);

            } elseif ($condition === 'bad') {
                $badSparepart = Sparepart::where('part_number', $originalSparepart->part_number)
                    ->where('condition', 'Rusak')
                    ->first();

                if ($badSparepart) {
                    $badSparepart->increment('stock', $qty);
                } else {
                    $badSparepart = $originalSparepart->replicate();
                    $badSparepart->condition = 'Rusak';
                    $badSparepart->stock = $qty;
                    $badSparepart->created_at = now();
                    $badSparepart->updated_at = now();
                    $badSparepart->save();
                }
                $this->logActivity('Pengembalian Barang (Rusak)', "Melaporkan pengembalian {$qty} unit '{$originalSparepart->name}' dalam kondisi Rusak.");

            } elseif ($condition === 'lost') {
                 $lostSparepart = Sparepart::where('part_number', $originalSparepart->part_number)
                     ->where('condition', 'Hilang')
                     ->first();
 
                 if ($lostSparepart) {
                     $lostSparepart->increment('stock', $qty);
                 } else {
                     $lostSparepart = $originalSparepart->replicate();
                     $lostSparepart->condition = 'Hilang';
                     $lostSparepart->stock = $qty;
                     $lostSparepart->created_at = now();
                     $lostSparepart->updated_at = now();
                     $lostSparepart->save();
                 }
                 $this->logActivity('Barang Hilang', "Melaporkan pengembalian {$qty} unit '{$originalSparepart->name}' dalam kondisi Hilang.");
            }
        });

        // 5. Final Log
        $conditionMap = [
            'good' => 'Baik',
            'bad' => 'Rusak',
            'lost' => 'Hilang'
        ];
        $translatedCondition = $conditionMap[$request->return_condition] ?? $request->return_condition;

        $this->logActivity('Pengembalian Barang', "Mengembalikan {$request->return_quantity} unit '{$borrowing->sparepart->name}' (Kondisi: {$translatedCondition}).");

        $message = 'Barang berhasil dikembalikan.';

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'status' => 'success'
            ]);
        }

        return back()->with('success', $message);
    }

    public function history(Borrowing $borrowing)
    {
        try {
            $borrowing->load('user', 'returns'); // Eager load

            return response()->json([
                'borrower' => $borrowing->user ? $borrowing->user->name : 'User Terhapus',
                'borrow_date' => $borrowing->borrowed_at ? $borrowing->borrowed_at->translatedFormat('d F Y H:i') : '-',
                'total_quantity' => $borrowing->quantity,
                'status' => $borrowing->status,
                'items' => $borrowing->returns->sortByDesc('created_at')->values()->map(function ($return) {
                    return [
                        'id' => $return->id,
                        'date' => $return->return_date ? $return->return_date->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                        'quantity' => $return->quantity,
                        'condition' => $return->condition,
                        'notes' => $return->notes ?? '',
                        'photos' => $return->photos ?? [],
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in BorrowingController::history', [
                'borrowing_id' => $borrowing->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => 'Terjadi kesalahan saat memuat riwayat',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
