<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SuperAdminDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Menampilkan dashboard utama SuperAdmin dengan statistik lengkap.
     * 
     * Menggunakan caching untuk performa dan filter berdasarkan tanggal.
     */
    public function index(Request $request)
    {
        // 1. Tentukan Rentang Tanggal
        [$start, $end, $period] = $this->dashboardService->getDateRange(
            $request->input('period'),
            $request->input('year'),
            $request->input('month')
        );

        $year = $request->input('year');
        $month = $request->input('month');
        $user = auth()->user();
        
        // Generate Cache Key berdasarkan filter, role user, dan timestamp update terakhir
        $lastUpdated = Cache::get('inventory_last_updated', 'init');
        $cacheKey = 'dashboard_stats_' . $period . '_' . ($year ?? 'nay') . '_' . ($month ?? 'nam') . '_' . $user->id . '_' . $lastUpdated;
        
        // Simpan cache selama 10 menit (600 detik)
        $data = Cache::remember($cacheKey, 600, function () use ($start, $end, $period, $user) {
            
            // --- Snapshot (Biasanya tidak difilter tanggal) ---
            $snapshots = $this->dashboardService->getStockSnapshots();
            
            // --- Analitik Terfilter ---
            $movementData = $this->dashboardService->getStockMovements($start, $end);
            $topExited = $this->dashboardService->getTopItems($start, $end, 'keluar');
            $topEntered = $this->dashboardService->getTopItems($start, $end, 'masuk');
            $deadStockItems = $this->dashboardService->getDeadStock($start, $end, $period);
            $activeUsers = $this->dashboardService->getUserLeaderboard($start, $end);
            $forecasts = $this->dashboardService->getForecasts($topExited);
            
            // --- Grafik ---
            $stockByCategory = $this->dashboardService->getStockByAttribute('category');
            $stockByLocation = $this->dashboardService->getStockByAttribute('location');
            
            // --- Peminjaman (Berbasis Role) ---
            $borrowingStats = $this->dashboardService->getBorrowingStats($user);
            
            // --- Aktivitas Terbaru ---
            $recentActivitiesRaw = $this->dashboardService->getRecentActivities($start, $end);
            $recentActivities = $recentActivitiesRaw->map(function ($log) {
                return [
                    'id' => $log->id,
                    'description' => $log->description,
                    'user_name' => $log->user->name ?? 'Sistem',
                    'created_at_diff' => $log->created_at->diffForHumans(),
                    // Properti asli jika masih dibutuhkan blade (walau redundan)
                    'user' => $log->user,
                    'created_at' => $log->created_at,
                ];
            });

            // --- Additional Data for Real-time Feeds ---
            $activeBorrowingsList = \App\Models\Borrowing::with(['sparepart' => function ($query) {
                    $query->withTrashed();
                }, 'user'])
                ->where('status', 'borrowed')
                ->latest()
                ->take(5)
                ->get();

            $overdueBorrowingsListRaw = \App\Models\Borrowing::with(['sparepart' => function ($query) {
                    $query->withTrashed();
                }, 'user'])
                ->where('status', 'borrowed')
                ->where('expected_return_at', '<', now())
                ->orderBy('expected_return_at', 'asc')
                ->take(5)
                ->get();
            
            $overdueBorrowingsList = $overdueBorrowingsListRaw->map(function ($borrow) {
                return [
                    'id' => $borrow->id,
                    'user_name' => $borrow->user->name ?? $borrow->borrower_name,
                    'sparepart_name' => $borrow->sparepart->name ?? 'Unknown item',
                    'quantity' => $borrow->quantity,
                    'due_date_formatted' => $borrow->expected_return_at->format('d M Y'),
                    'due_date_rel' => $borrow->expected_return_at->diffForHumans(['parts' => 1]),
                    // Original objects for Blade
                    'user' => $borrow->user,
                    'sparepart' => $borrow->sparepart,
                    'expected_return_at' => $borrow->expected_return_at,
                    'borrower_name' => $borrow->borrower_name,
                ];
            });

            return array_merge(
                $snapshots, 
                [
                    'movementData' => $movementData,
                    'topExited' => $topExited,
                    'topEntered' => $topEntered,
                    'deadStockItems' => $deadStockItems,
                    'activeUsers' => $activeUsers,
                    'forecasts' => $forecasts,
                    'stockByCategory' => $stockByCategory,
                    'stockByLocation' => $stockByLocation,
                    'recentActivities' => $recentActivities,
                    'activeBorrowingsList' => $activeBorrowingsList, 
                    'overdueBorrowingsList' => $overdueBorrowingsList,
                ],
                $borrowingStats
            );
        });

        if ($request->wantsJson()) {
            return response()->json($data);
        }

        // Barang bertipe 'sale' yang belum memiliki harga — selalu fresh (tidak di-cache)
        $noPriceItems = \App\Models\Sparepart::where('type', 'sale')
            ->where(function ($q) {
                $q->whereNull('price')->orWhere('price', '<=', 0);
            })
            ->latest()
            ->take(10)
            ->get();

        return view('dashboard.superadmin', array_merge($data, [
            'period'       => $period,
            'start'        => $start,
            'end'          => $end,
            'year'         => $year,
            'month'        => $month,
            'noPriceItems' => $noPriceItems,
        ]));
    }

    /**
     * Endpoint AJAX ringan untuk quick-filter per-widget "Pergerakan Stok".
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
