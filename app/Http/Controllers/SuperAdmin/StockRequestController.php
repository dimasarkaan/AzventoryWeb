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

        $stockLog = $inventory->stockLogs()->create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'quantity' => $request->quantity,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        $this->logActivity('Pengajuan Stok', "Pengajuan stok {$request->type} sebanyak {$request->quantity} untuk '{$inventory->name}' dengan alasan '{$request->reason}'.");

        // Notify admins and superadmins
        $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
        $message = "Pengajuan stok {$stockLog->type} baru untuk {$inventory->name} oleh " . Auth::user()->name;
        Notification::send($admins, new StockRequestNotification($stockLog, $message));

        return redirect()->route('superadmin.inventory.show', $inventory)
            ->with('success', 'Pengajuan perubahan stok berhasil dikirim.');
    }
}
