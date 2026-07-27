<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

// Controller utama penggerak halaman Dashboard khusus SuperAdmin (Petinggi Tertinggi).
// Menangani penarikan data metrik berskala besar (Big Data) dari InventoryService dan menyajikannya ke dalam grafik/tabel.
class SuperAdminDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    // Menampilkan halaman utama Dashboard SuperAdmin beserta semua panel grafiknya
    // Dilengkapi dengan sistem Cache (Penyimpanan Sementara) agar halaman tidak lemot (timeout) saat data inventaris sudah sangat banyak
    public function index(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'period' => 'nullable|string|in:today,this_week,this_month,this_year,custom,custom_year,custom_range',
            'year' => 'nullable|integer|min:2000|max:'.(now()->year + 1),
            'month' => 'nullable|string|regex:/^([1-9]|1[0-2]|all|)$/',
            'start_date' => 'nullable|date',
            'end_date' => [
                'nullable',
                'date',
                'after_or_equal:start_date',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->start_date && $value) {
                        $diff = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($value));
                        if ($diff > 365) {
                            $fail('Rentang tanggal maksimal 365 hari.');
                        }
                    }
                },
            ],
        ]);

        // 2. Tentukan Rentang Tanggal
        [$start, $end, $period] = $this->dashboardService->getDateRange(
            $request->input('period'),
            $request->input('year'),
            $request->input('month'),
            $request->input('start_date'),
            $request->input('end_date')
        );

        $year = $request->input('year');
        $month = $request->input('month');
        $user = auth()->user();

        // Generate Cache Key berdasarkan filter, role user, dan timestamp update terakhir
        $lastUpdated = Cache::get('inventory_last_updated', 'init');
        $cacheKey = 'dashboard_stats_'.$period.'_'.($year ?? 'nay').'_'.($month ?? 'nam').'_'.$user->id.'_'.$lastUpdated;

        // Simpan hasil hitungan berat ini ke Cache selama 10 menit (600 detik)
        // (Catatan: tipe kembalian dideklarasikan untuk mencegah error intelephense PHP6613)
        /** @return array<string, mixed> */
        $data = Cache::remember($cacheKey, 600, function () use ($start, $end, $period, $user): array {

            // --- Snapshot (Biasanya tidak difilter tanggal) ---
            $snapshots = $this->dashboardService->getStockSnapshots();

            // --- Analitik Terfilter ---
            $movementData = $this->dashboardService->getStockMovements($start, $end);
            $topExited = $this->dashboardService->getTopItems($start, $end, 'keluar');
            $topEntered = $this->dashboardService->getTopItems($start, $end, 'masuk');
            $deadStockItems = $this->dashboardService->getDeadStock($start, $end, $period);
            $activeUsers = $this->dashboardService->getUserLeaderboard($start, $end);

            // --- Grafik ---
            $stockByCategory = $this->dashboardService->getStockByAttribute('category');
            $stockByLocation = $this->dashboardService->getStockByAttribute('location');

            // --- Peminjaman (Berbasis Role) ---
            $borrowingStats = $this->dashboardService->getBorrowingStats($user);

            // --- Aktivitas Terbaru ---
            $recentActivitiesRaw = $this->dashboardService->getRecentActivities();
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
                ->active()
                ->latest()
                ->take(5)
                ->get();

            $overdueBorrowingsListRaw = \App\Models\Borrowing::with(['sparepart' => function ($query) {
                $query->withTrashed();
            }, 'user'])
                ->overdue()
                ->orderBy('expected_return_at', 'asc')
                ->take(5)
                ->get();

            $overdueBorrowingsList = $overdueBorrowingsListRaw->map(function ($borrow) {
                return [
                    'id' => $borrow->id,
                    'sparepart_uuid' => $borrow->sparepart->uuid ?? null,
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

            // Deklarasi array untuk menghindari error penggabungan data di Editor (PHP6613)
            /** @var array<string, mixed> $result */
            $result = array_merge(
                $snapshots,
                [
                    'movementData' => $movementData,
                    'topExited' => $topExited,
                    'topEntered' => $topEntered,
                    'deadStockItems' => $deadStockItems,
                    'activeUsers' => $activeUsers,
                    'stockByCategory' => $stockByCategory,
                    'stockByLocation' => $stockByLocation,
                    'recentActivities' => $recentActivities,
                    'activeBorrowingsList' => $activeBorrowingsList,
                    'overdueBorrowingsList' => $overdueBorrowingsList,
                ],
                $borrowingStats
            );
            return $result;
        });

        // --- Stok Menipis (max 5) ---
        $lowStockItemsRaw = \App\Models\Sparepart::with('category')
            ->lowStock()
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        $lowStockItems = $lowStockItemsRaw->map(function ($item) {
            return [
                'id' => $item->id,
                'uuid' => $item->uuid ?? null,
                'name' => $item->name,
                'stock' => $item->stock,
                'minimum_stock' => $item->minimum_stock,
                'category' => ['name' => $item->category->name ?? 'Unknown'],
                'category_name' => $item->category->name ?? 'Unknown',
            ];
        });

        // Barang bertipe 'sale' yang belum memiliki harga — selalu fresh (tidak di-cache)
        $noPriceItemsRaw = \App\Models\Sparepart::noPrice()
            ->latest()
            ->take(10)
            ->get();

        // Map data agar aman dikonsumsi oleh JavaScript/Alpine
        $noPriceItems = $noPriceItemsRaw->map(function ($item) {
            return [
                'id' => $item->id,
                'uuid' => $item->uuid ?? null,
                'name' => $item->name,
                'part_number' => $item->part_number,
                'price' => $item->price,
            ];
        });

        if ($request->wantsJson()) {
            return response()->json(array_merge($data, [
                'noPriceItems' => $noPriceItems,
                'lowStockItems' => $lowStockItems,
            ]));
        }

        return view('dashboard.superadmin', array_merge($data, [
            'period' => $period,
            'start' => $start,
            'end' => $end,
            'year' => $year,
            'month' => $month,
            'noPriceItems' => $noPriceItems,
            'lowStockItems' => $lowStockItems,
            'availableYears' => $this->dashboardService->getAvailableYears(),
        ]));
    }

    // Fitur 'Jalur Cepat' (Endpoint AJAX) khusus untuk memperbarui data grafik "Pergerakan Stok"
    // Dipanggil saat pengguna mengeklik tombol filter [7 Hari] atau [30 Hari] di atas grafik, sehingga tidak perlu me-refresh satu halaman penuh.
    public function movementData(Request $request)
    {
        $range = (int) $request->input('range', 7);

        // Batasi range agar tidak bisa di-abuse (max 365 hari)
        $range = max(1, min($range, 365));

        $start = now()->subDays($range - 1)->startOfDay();
        $end = now()->endOfDay();

        $data = $this->dashboardService->getStockMovements($start, $end);

        return response()->json($data);
    }
}
