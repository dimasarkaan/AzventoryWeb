<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use App\Models\User;
use App\Notifications\StockRequestNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ActivityLogger;

class StockRequestController extends Controller
{
    use ActivityLogger;
    public function create(Sparepart $inventory)
    {
        return view('superadmin.inventory.stock_request', ['sparepart' => $inventory]);
    }

    public function store(Request $request, Sparepart $inventory)
    {
        $request->validate([
            'type' => 'required|in:masuk,keluar',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $isAutoApproved = in_array($user->role, ['superadmin', 'admin']);
        $status = $isAutoApproved ? 'approved' : 'pending';
        $approvedBy = $isAutoApproved ? $user->id : null;

        // DB Transaction to ensure data consistency
        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $inventory, $user, $status, $approvedBy, $isAutoApproved) {
            
            // If Auto Approved, update stock immediately
            if ($isAutoApproved) {
                if ($request->type === 'masuk') {
                    $inventory->stock += $request->quantity;
                } else { // keluar
                    if ($inventory->stock < $request->quantity) {
                         throw \Illuminate\Validation\ValidationException::withMessages(['quantity' => 'Stok tidak mencukupi untuk pengurangan ini.']);
                    }
                    $inventory->stock -= $request->quantity;
                }
                $inventory->save();
            }

            // Create Log
            $stockLog = $inventory->stockLogs()->create([
                'user_id' => $user->id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'reason' => $request->reason,
                'status' => $status,
                'approved_by' => $approvedBy,
            ]);

            // Notifications & Activity Logging
            if ($isAutoApproved) {
                // Determine Log Title
                $actionTitle = $request->type === 'masuk' ? 'Penambahan Stok' : 'Pengurangan Stok';
                $this->logActivity($actionTitle, "{$actionTitle}: {$request->quantity} {$inventory->unit} untuk '{$inventory->name}'. Alasan: {$request->reason}");
                
                // Low Stock Notification Check (if reduced)
                if ($request->type === 'keluar' && $inventory->minimum_stock > 0 && $inventory->stock <= $inventory->minimum_stock) {
                    $admins = User::whereIn('role', ['superadmin', 'admin'])->get();
                     Notification::send($admins, new \App\Notifications\LowStockNotification($inventory));
                }

            } else {
                // Pending Request Logic
                $this->logActivity('Pengajuan Stok', "Pengajuan stok {$request->type} sebanyak {$request->quantity} untuk '{$inventory->name}' dengan alasan '{$request->reason}'.");
                
                // Notify admins
                $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
                $message = "Pengajuan stok {$stockLog->type} baru untuk {$inventory->name} oleh " . $user->name;
                Notification::send($admins, new StockRequestNotification($stockLog, $message));
            }
        });

        $message = $isAutoApproved 
            ? 'Stok berhasil diperbarui secara langsung.' 
            : 'Pengajuan perubahan stok berhasil dikirim, menunggu persetujuan Admin/Superadmin.';

        return redirect()->route('superadmin.inventory.show', $inventory)
            ->with('success', $message);
    }
}
