<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use App\Notifications\StockRequestNotification;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

// Controller yang bertugas mengatur pengajuan perubahan stok (Tambah / Kurang Barang).
// Membedakan hak akses: Admin mengubah langsung, sementara Operator harus mengajukan permohonan dan menunggu persetujuan (Acc).
class StockRequestController extends Controller
{
    use ActivityLogger;

    protected $inventoryService;

    public function __construct(\App\Services\InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    // Menampilkan halaman formulir pengajuan perubahan stok (Tambah / Kurang)
    public function create(Sparepart $inventory)
    {
        $this->authorize('create', StockLog::class);

        return view('inventory.stock_request', ['sparepart' => $inventory]);
    }

    // Memproses data formulir saat tombol "Ajukan" atau "Simpan" ditekan
    public function store(Request $request, Sparepart $sparepart)
    {
        $this->authorize('create', StockLog::class);

        // Aturan Validasi: Pastikan tipe mutasi jelas, angkanya valid (tidak minus), dan alasannya masuk akal
        $rules = [
            'type' => 'required|in:masuk,keluar',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ];

        // Mencegah permohonan "Barang Keluar" kalau permintaannya lebih rakus daripada sisa stok di gudang
        if ($request->type === 'keluar') {
            $rules['quantity'] .= '|max:'.$sparepart->stock;
        }

        $request->validate($rules, [
            'quantity.max' => "Permintaan dibatalkan: Stok fisik hanya tersisa {$sparepart->stock}. Anda tidak dapat meminta stok lebih dari jumlah yang tersedia.",
        ]);

        $user = Auth::user();
        
        // Pengecekan Kasta (Role): Superadmin dan Admin punya jalur VIP, perubahan stok akan otomatis langsung di-ACC (Approved).
        // Sedangkan Operator masuk ke jalur antrean (Pending).
        $isAutoApproved = in_array($user->role, [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN]);
        $status = $isAutoApproved ? 'approved' : 'pending';
        $approvedBy = $isAutoApproved ? $user->id : null;

        // Membungkus seluruh proses ini ke dalam Transaksi Database
        // Ini adalah asuransi. Kalau sistem tiba-tiba error/mati listrik di tengah proses, databasenya nggak akan berantakan atau tersimpan setengah-setengah (Rollback).
        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $sparepart, $user, $status, $approvedBy, $isAutoApproved) {

            // --- JALUR VIP: Eksekusi Langsung (Hanya Admin / Superadmin) ---
            if ($isAutoApproved) {
                if ($request->type === 'masuk') {
                    $sparepart->stock += $request->quantity;
                } else { // keluar
                    // Pengecekan kedua kali demi keamanan super ketat, mencegah bug stok minus
                    if ($sparepart->stock < $request->quantity) {
                        throw \Illuminate\Validation\ValidationException::withMessages(['quantity' => 'Stok tidak mencukupi untuk pengurangan ini.']);
                    }
                    $sparepart->stock -= $request->quantity;
                }
                $sparepart->save();

                // Bersihkan Cache memori Dashboard supaya grafik dan ringkasan langsung merefleksikan jumlah stok terbaru
                $this->inventoryService->clearCache();

                // Teriak pakai speaker (Broadcast) ke seluruh user yang sedang buka aplikasi bahwa ada stok yang diubah
                $actionType = $request->type === 'masuk' ? 'success' : 'warning';
                $actionText = $request->type === 'masuk' ? 'menambah stok' : 'mengurangi stok';
                $customMessage = "{$user->name} {$actionText} sebanyak {$request->quantity} {$sparepart->unit} pada barang: {$sparepart->name}";
                $this->inventoryService->broadcastUpdate($sparepart, $actionType, $customMessage);
            }

            // Catat bukti permohonan ke dalam tabel riwayat/log mutasi (berlaku untuk admin maupun operator)
            $stockLog = $sparepart->stockLogs()->create([
                'user_id' => $user->id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'reason' => $request->reason,
                'status' => $status,
                'approved_by' => $approvedBy,
            ]);

            // ================= PENANGANAN NOTIFIKASI & AUDIT TRAIL =================
            if ($isAutoApproved) {
                
                // Catat secara formal di tabel Riwayat Aktivitas Sistem
                $actionTitle = $request->type === 'masuk' ? 'Penambahan Stok' : 'Pengurangan Stok';

                $changes = [
                    'stock' => [
                        'old' => $sparepart->getOriginal('stock'),
                        'new' => $sparepart->stock,
                    ],
                ];

                $this->logActivity($actionTitle, "{$actionTitle}: {$request->quantity} {$sparepart->unit} untuk '{$sparepart->name}'. Alasan: {$request->reason}", $changes);

                // Early Warning System (Sistem Peringatan Dini)
                // Jika stok keluar menyebabkan barang menyentuh limit krisis (minimum stock), otomatis kirim peringatan ke Admin
                if ($request->type === 'keluar') {
                    if ($sparepart->minimum_stock > 0 && $sparepart->stock <= $sparepart->minimum_stock) {
                        $admins = User::whereIn('role', [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])->get();
                        Notification::send($admins, new \App\Notifications\LowStockNotification($sparepart));
                        
                        $severity = $sparepart->stock === 0 ? 'depleted' : 'critical';
                        try {
                            broadcast(new \App\Events\StockCriticalEvent($sparepart, $severity));
                        } catch (\Throwable $e) {
                        }
                    } elseif ($sparepart->minimum_stock > 0 && $sparepart->stock <= ($sparepart->minimum_stock + 5)) {
                        $admins = User::whereIn('role', [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])->get();
                        Notification::send($admins, new \App\Notifications\ApproachingStockNotification($sparepart));
                    }
                }

            } else {
                // --- JALUR REGULER: Menunggu Persetujuan (Hanya Operator) ---
                
                // Karena stoknya belum berubah, kita cukup mencatat log "Pengajuan"-nya saja
                $this->logActivity('Pengajuan Stok', "Pengajuan stok {$request->type} sebanyak {$request->quantity} untuk '{$sparepart->name}' dengan alasan '{$request->reason}'.");

                // Lempar event realtime ke layar Admin supaya muncul bunyi "Ting!" notifikasi pop-up permintaan baru
                try {
                    broadcast(new \App\Events\StockApprovalUpdatedEvent($stockLog->fresh(), 'created'))->toOthers();
                } catch (\Exception $e) {
                }

                // Masukkan notifikasi resmi ke sistem lonceng Admin (Hanya Admin yang berhak mem-validasi/meng-ACC permohonan ini)
                $admins = User::where('role', \App\Enums\UserRole::ADMIN)->get();
                $type = $request->type == 'masuk' ? __('ui.type_in') : __('ui.type_out');
                $message = __('ui.notification_new_stock_request', [
                    'type' => $type,
                    'name' => $sparepart->name,
                    'user' => $user->name,
                ]);
                Notification::send($admins, new StockRequestNotification($stockLog, $message));
            }
        });

        $message = $isAutoApproved
            ? 'Stok berhasil diperbarui secara langsung.'
            : 'Pengajuan perubahan stok berhasil dikirim, menunggu persetujuan Admin.';

        return redirect()->route('inventory.show', $sparepart)
            ->with('success', $message);
    }
}
