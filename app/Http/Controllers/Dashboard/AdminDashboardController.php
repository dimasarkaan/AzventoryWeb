<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

// Controller khusus untuk menampilkan ringkasan data (Dashboard) bagi role Admin.
// Menampilkan grafik statistik, daftar peminjaman aktif, dan log aktivitas.
// Catatan Hak Akses Data:
// - Peminjaman & Aktivitas: Hanya menampilkan riwayat milik Admin dan Operator (tidak bisa melihat aktivitas Superadmin).
// - Stok Gudang: Menampilkan keseluruhan barang yang ada.
class AdminDashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    // Menampilkan halaman dashboard Admin beserta seluruh data grafiknya
    // Mendukung fitur penyaringan data berdasarkan rentang waktu (hari, bulan, tahun)
    public function index(Request $request)
    {
        // 1. Validasi Inputan Waktu
        // Memastikan parameter filter waktu yang dikirimkan tidak dimanipulasi
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
                        // Batasi rentang pencarian manual maksimal 1 tahun (365 hari) agar proses muat tidak berat
                        $diff = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($value));
                        if ($diff > 365) {
                            $fail('Rentang tanggal maksimal 365 hari.');
                        }
                    }
                },
            ],
        ]);

        // 2. Menerjemahkan pilihan waktu dari user menjadi format Tanggal Awal dan Akhir (Start & End)
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

        // 3. Membuat Nama Unik (Cache Key) untuk menyimpan sementara hasil hitungan di memori Server
        // Berguna agar halaman dimuat super cepat jika tidak ada update barang terbaru
        $lastUpdated = Cache::get('inventory_last_updated', 'init');
        $cacheKey = 'admin_dashboard_stats_'.$period
                     .'_'.($year ?? 'nay')
                     .'_'.($month ?? 'nam')
                     .'_'.$user->id
                     .'_'.$lastUpdated;

        // 4. Menarik data dari Cache (jika tersedia), atau hitung ulang semua rumus jika cachenya kadaluarsa
        /** @return array<string, mixed> */
        $data = Cache::remember($cacheKey, 600, function () use ($start, $end, $period, $user): array {

            // --- Menarik total keseluruhan data barang di gudang secara real-time ---
            $snapshots = $this->dashboardService->getStockSnapshots();

            // --- Menghitung jumlah lalu lintas barang masuk/keluar sesuai rentang waktu ---
            $movementData = $this->dashboardService->getStockMovements($start, $end);
            $topExited = $this->dashboardService->getTopItems($start, $end, 'keluar');
            $topEntered = $this->dashboardService->getTopItems($start, $end, 'masuk');
            $deadStockItems = $this->dashboardService->getDeadStock($start, $end, $period);

            // --- Daftar peringkat pengguna paling aktif (hanya mengukur Operator dan sesama Admin) ---
            $activeUsers = $this->dashboardService->getUserLeaderboard($start, $end);

            // --- Kumpulan data ringkas untuk membangun grafik lingkaran (Pie Chart) ---
            $stockByCategory = $this->dashboardService->getStockByAttribute('category');
            $stockByLocation = $this->dashboardService->getStockByAttribute('location');

            // --- Menarik statistik peminjaman barang khusus dalam scope otoritas admin ---
            $borrowingStats = $this->dashboardService->getBorrowingStats($user);

            // --- Daftar sejarah aktivitas (Audit Trail) yang baru saja terjadi ---
            $recentActivitiesRaw = $this->dashboardService->getRecentActivities($user);
            $recentActivities = $recentActivitiesRaw->map(function ($log) {
                return [
                    'id' => $log->id,
                    'description' => $log->description,
                    'user_name' => $log->user->name ?? 'Sistem',
                    'created_at_diff' => $log->created_at->diffForHumans(), // format "1 jam yang lalu"
                    'user' => $log->user,
                    'created_at' => $log->created_at,
                ];
            });

            // --- Menarik maksimal 5 data peminjaman barang yang belum dikembalikan ---
            $activeBorrowingsList = \App\Models\Borrowing::with([
                'sparepart' => fn ($q) => $q->withTrashed(), // Tetap tampilkan meski barang aslinya sudah di-soft delete
                'user',
            ])
                ->active()
                ->whereIn('user_id', function ($q) {
                    // Admin hanya bisa mengawasi peminjaman dari Admin lain dan Operator
                    $q->select('id')
                        ->from('users')
                        ->whereIn('role', [
                            \App\Enums\UserRole::OPERATOR,
                            \App\Enums\UserRole::ADMIN,
                        ]);
                })
                ->latest()
                ->take(5)
                ->get();

            // --- Menarik maksimal 5 data peminjaman yang sudah melewati batas pengembalian (Overdue) ---
            $overdueRaw = \App\Models\Borrowing::with([
                'sparepart' => fn ($q) => $q->withTrashed(),
                'user',
            ])
                ->overdue()
                ->whereIn('user_id', function ($q) {
                    $q->select('id')
                        ->from('users')
                        ->whereIn('role', [
                            \App\Enums\UserRole::OPERATOR,
                            \App\Enums\UserRole::ADMIN,
                        ]);
                })
                ->orderBy('expected_return_at', 'asc') // Urutkan dari yang paling terlambat
                ->take(5)
                ->get();

            // --- Menarik maksimal 5 jenis barang yang sisa stoknya sangat kritis (kritis = di bawah minimum) ---
            $lowStockItemsRaw = \App\Models\Sparepart::with('category')
                ->lowStock()
                ->orderBy('stock', 'asc')
                ->take(5)
                ->get();

            // Menyusun format array ringkas untuk data stok kritis
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

            // Mengemas data keterlambatan peminjaman menjadi array standar agar mudah dibaca oleh komponen JavaScript di frontend
            $overdueBorrowingsList = $overdueRaw->map(function ($borrow) {
                return [
                    'id' => $borrow->id,
                    'sparepart_uuid' => $borrow->sparepart->uuid ?? null,
                    'user_name' => $borrow->user->name ?? $borrow->borrower_name,
                    'sparepart_name' => $borrow->sparepart->name ?? 'Unknown item',
                    'quantity' => $borrow->quantity,
                    'due_date_formatted' => $borrow->expected_return_at->format('d M Y'),
                    'due_date_rel' => $borrow->expected_return_at->diffForHumans(['parts' => 1]),
                    'user' => $borrow->user,
                    'sparepart' => $borrow->sparepart,
                    'expected_return_at' => $borrow->expected_return_at,
                    'borrower_name' => $borrow->borrower_name,
                ];
            });

            // Satukan seluruh komponen yang sudah kita tarik ke dalam 1 bungkusan wadah yang utuh
            // Memecah proses penggabungan agar IDE (Intelephense) tidak menampilkan peringatan error PHP6613
            /** @var array<string, mixed> $result */
            $result = [];
            $result = array_merge($result, $snapshots);
            $result['movementData'] = $movementData;
            $result['topExited'] = $topExited;
            $result['topEntered'] = $topEntered;
            $result['deadStockItems'] = $deadStockItems;
            $result['activeUsers'] = $activeUsers;
            $result['stockByCategory'] = $stockByCategory;
            $result['stockByLocation'] = $stockByLocation;
            $result['recentActivities'] = $recentActivities;
            $result['activeBorrowingsList'] = $activeBorrowingsList;
            $result['overdueBorrowingsList'] = $overdueBorrowingsList;
            $result['lowStockItems'] = $lowStockItems;
            $result = array_merge($result, $borrowingStats);

            return $result;
        });

        // 5. Kembalikan data dalam format JSON jika yang meminta (me-request) adalah script AJAX
        // Biasanya fitur ini dipakai saat grafik ingin di-refresh otomatis tanpa pindah halaman
        if ($request->wantsJson()) {
            return response()->json($data);
        }

        // 6. Namun, jika dibuka secara normal, muat halaman HTML (View) Dashboard utamanya
        return view('dashboard.admin', array_merge($data, [
            'period' => $period,
            'start' => $start,
            'end' => $end,
            'year' => $year,
            'month' => $month,
            'availableYears' => $this->dashboardService->getAvailableYears(),
        ]));
    }

    // Endpoint ringan (AJAX API) untuk memuat data grafik bar "Pergerakan Stok" di halaman dashboard
    // Fitur ini digunakan saat Admin memencet opsi 7 Hari / 30 Hari / 90 Hari yang ada di pojok grafik
    public function movementData(Request $request)
    {
        $range = (int) $request->input('range', 7);

        // Pengamanan ringan: Mencegah user mengutak-atik sistem dengan memuat data lebih dari setahun
        $range = max(1, min($range, 365));

        // Menentukan kapan batas hari pertama penarikan data dan batas waktu hari akhirnya (hari ini)
        $start = now()->subDays($range - 1)->startOfDay();
        $end = now()->endOfDay();

        // Melemparkan kalkulasi grafik ke DashboardService
        $data = $this->dashboardService->getStockMovements($start, $end);

        // Kembalikan dalam balutan JSON ke Javascript untuk digambar ke dalam grafik
        return response()->json($data);
    }
}
