<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ActivityLogger;
use App\Http\Requests\Inventory\Borrowing\StoreBorrowingRequest;
use App\Http\Requests\Inventory\Borrowing\ReturnBorrowingRequest;
use App\Models\StockLog;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Notification;
use App\Notifications\LowStockNotification;

class BorrowingController extends Controller
{
    use ActivityLogger;
    protected $imageOptimizer;
    protected $inventoryService;

    public function __construct(\App\Services\ImageOptimizationService $imageOptimizer, \App\Services\InventoryService $inventoryService)
    {
        $this->imageOptimizer = $imageOptimizer;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Menampilkan detail peminjaman dan riwayat pengembalian.
     */
    public function show(Borrowing $borrowing)
    {
        $this->authorize('view', $borrowing);

        $borrowing->load(['user', 'sparepart', 'returns' => function($query) {
            $query->orderBy('return_date', 'desc');
        }]);

        return view('inventory.borrow.show', [
            'borrowing' => $borrowing
        ]);
    }

    /**
     * Menyimpan data peminjaman baru (Pinjam Barang).
     */
    public function store(StoreBorrowingRequest $request, Sparepart $sparepart)
    {
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

            // Periksa jika stok mencapai atau di bawah minimum
            $sparepart->refresh();
            if ($sparepart->stock <= $sparepart->minimum_stock) {
                $superadmins = User::where('role', UserRole::SUPERADMIN)->get();
                Notification::send($superadmins, new LowStockNotification($sparepart));
            }

            // Log Activity
            $this->logActivity('Peminjaman Barang', "Meminjam {$request->quantity} {$sparepart->unit} '{$sparepart->name}'. Catatan: " . ($request->notes ?? '-'));

            // Clear Dashboard Cache & Broadcast Update
            $this->inventoryService->clearCache();
            $this->inventoryService->broadcastUpdate($sparepart, 'updated');
        });

        return redirect()->route('inventory.show', $sparepart->id)
            ->with('success', 'Peminjaman berhasil dicatat.');
    }

    /**
     * Memperbarui status peminjaman (Kembalikan Barang).
     */
    public function returnItem(ReturnBorrowingRequest $request, Borrowing $borrowing)
    {
        $this->authorize('update', $borrowing);

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
                
                StockLog::create([
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

            // Clear Dashboard Cache & Broadcast Update
            $this->inventoryService->clearCache();
            $this->inventoryService->broadcastUpdate($originalSparepart, 'updated');
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
        $this->authorize('view', $borrowing);

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
