<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Controller Dashboard Admin
 *
 * Menampilkan ringkasan statistik inventaris untuk role Admin.
 * Scope data berbeda dari Superadmin:
 *  - Peminjaman aktif/terlambat: hanya Admin + Operator (bukan semua user)
 *  - Aktivitas terbaru: hanya log dari Admin + Operator (bukan Superadmin)
 *  - Inventaris & stok: semua data (Admin bisa kelola semua barang)
 */
class AdminDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Menampilkan halaman dashboard Admin dengan statistik lengkap.
     *
     * Mendukung filter periode (hari ini, minggu ini, bulan ini, tahun ini, custom)
     * dan mengembalikan JSON jika request meminta format tersebut (untuk real-time refresh).
     */
    public function index(Request $request)
    {
        // 1. Tentukan Rentang Tanggal berdasarkan filter periode dari request
        [$start, $end, $period] = $this->dashboardService->getDateRange(
            $request->input('period'),
            $request->input('year'),
            $request->input('month')
        );

        $year  = $request->input('year');
        $month = $request->input('month');
        $user  = auth()->user();

        // 2. Generate Cache Key unik berdasarkan filter & user
        //    Disertai timestamp update terakhir inventaris untuk invalidasi otomatis
        $lastUpdated = Cache::get('inventory_last_updated', 'init');
        $cacheKey    = 'admin_dashboard_stats_' . $period
                     . '_' . ($year ?? 'nay')
                     . '_' . ($month ?? 'nam')
                     . '_' . $user->id
                     . '_' . $lastUpdated;

        // 3. Ambil data dengan cache 10 menit (600 detik)
        $data = Cache::remember($cacheKey, 600, function () use ($start, $end, $period, $user) {

            // --- Snapshot: Statistik global inventaris (tidak difilter tanggal) ---
            $snapshots = $this->dashboardService->getStockSnapshots();

            // --- Analitik berdasarkan periode yang dipilih ---
            $movementData  = $this->dashboardService->getStockMovements($start, $end);
            $topExited     = $this->dashboardService->getTopItems($start, $end, 'keluar');
            $topEntered    = $this->dashboardService->getTopItems($start, $end, 'masuk');
            $deadStockItems = $this->dashboardService->getDeadStock($start, $end, $period);

            // Leaderboard: Admin hanya ingin melihat aktivitas Operator di bawahnya
            $activeUsers = $this->dashboardService->getUserLeaderboard($start, $end);

            // --- Grafik distribusi stok ---
            $stockByCategory = $this->dashboardService->getStockByAttribute('category');
            $stockByLocation = $this->dashboardService->getStockByAttribute('location');

            // --- Statistik peminjaman: di-scope hanya Admin + Operator ---
            $borrowingStats = $this->dashboardService->getBorrowingStats($user);

            // --- Aktivitas terbaru: disaring berdasarkan role (Admin + Operator saja) ---
            $recentActivitiesRaw = $this->dashboardService->getRecentActivities($start, $end, $user);
            $recentActivities    = $recentActivitiesRaw->map(function ($log) {
                return [
                    'id'               => $log->id,
                    'description'      => $log->description,
                    'user_name'        => $log->user->name ?? 'Sistem',
                    'created_at_diff'  => $log->created_at->diffForHumans(),
                    'user'             => $log->user,
                    'created_at'       => $log->created_at,
                ];
            });

            // --- Daftar peminjaman aktif (max 5) — scope Admin + Operator ---
            $activeBorrowingsList = \App\Models\Borrowing::with([
                    'sparepart' => fn($q) => $q->withTrashed(),
                    'user',
                ])
                ->where('status', 'borrowed')
                ->whereIn('user_id', function ($q) use ($user) {
                    // Admin melihat peminjaman dirinya sendiri + semua Operator
                    $q->select('id')
                      ->from('users')
                      ->where(fn($inner) => $inner
                          ->where('role', \App\Enums\UserRole::OPERATOR)
                          ->orWhere('id', $user->id)
                      );
                })
                ->latest()
                ->take(5)
                ->get();

            // --- Daftar peminjaman terlambat (max 5) — scope Admin + Operator ---
            $overdueRaw = \App\Models\Borrowing::with([
                    'sparepart' => fn($q) => $q->withTrashed(),
                    'user',
                ])
                ->where('status', 'borrowed')
                ->where('expected_return_at', '<', now())
                ->whereIn('user_id', function ($q) use ($user) {
                    $q->select('id')
                      ->from('users')
                      ->where(fn($inner) => $inner
                          ->where('role', \App\Enums\UserRole::OPERATOR)
                          ->orWhere('id', $user->id)
                      );
                })
                ->orderBy('expected_return_at', 'asc')
                ->take(5)
                ->get();

            // Mapping ke format yang aman untuk di-pass ke JavaScript
            $overdueBorrowingsList = $overdueRaw->map(function ($borrow) {
                return [
                    'id'                 => $borrow->id,
                    'user_name'          => $borrow->user->name ?? $borrow->borrower_name,
                    'sparepart_name'     => $borrow->sparepart->name ?? 'Unknown item',
                    'quantity'           => $borrow->quantity,
                    'due_date_formatted' => $borrow->expected_return_at->format('d M Y'),
                    'due_date_rel'       => $borrow->expected_return_at->diffForHumans(['parts' => 1]),
                    'user'               => $borrow->user,
                    'sparepart'          => $borrow->sparepart,
                    'expected_return_at' => $borrow->expected_return_at,
                    'borrower_name'      => $borrow->borrower_name,
                ];
            });

            return array_merge(
                $snapshots,
                [
                    'movementData'          => $movementData,
                    'topExited'             => $topExited,
                    'topEntered'            => $topEntered,
                    'deadStockItems'        => $deadStockItems,
                    'activeUsers'           => $activeUsers,
                    'stockByCategory'       => $stockByCategory,
                    'stockByLocation'       => $stockByLocation,
                    'recentActivities'      => $recentActivities,
                    'activeBorrowingsList'  => $activeBorrowingsList,
                    'overdueBorrowingsList' => $overdueBorrowingsList,
                ],
                $borrowingStats
            );
        });

        // 4. Kembalikan JSON jika request dari AJAX (real-time refresh)
        if ($request->wantsJson()) {
            return response()->json($data);
        }

        // 5. Render view dashboard Admin
        return view('dashboard.admin', array_merge($data, [
            'period' => $period,
            'start'  => $start,
            'end'    => $end,
            'year'   => $year,
            'month'  => $month,
        ]));
    }

    /**
     * Endpoint AJAX ringan untuk quick-filter grafik Pergerakan Stok.
     *
     * Dipanggil oleh tombol [7 Hari] [30 Hari] [3 Bulan] di widget chart.
     * Hanya menghitung data movement — jauh lebih cepat dari endpoint utama.
     *
     * Query param: ?range=7|30|90 (hari)
     */
    public function movementData(Request $request)
    {
        $range = (int) $request->input('range', 7);

        // Batasi range agar tidak bisa di-abuse (max 365 hari)
        $range = max(1, min($range, 365));

        $start = now()->subDays($range - 1)->startOfDay();
        $end   = now()->endOfDay();

        $data = $this->dashboardService->getStockMovements($start, $end);

        return response()->json($data);
    }
}
