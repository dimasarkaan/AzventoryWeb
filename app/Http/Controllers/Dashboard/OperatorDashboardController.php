<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

// Controller khusus untuk menampilkan halaman Dashboard bagi role Operator.
// Hanya menampilkan data statistik, riwayat pinjaman, dan aktivitas yang dimiliki oleh Operator itu sendiri (Pribadi).
class OperatorDashboardController extends Controller
{
    // Menampilkan halaman utama dashboard Operator beserta grafik dan ringkasan datanya
    public function index()
    {
        $userId = auth()->id();
        
        // Mengambil penanda waktu kapan sistem terakhir kali di-update (dari memori Cache)
        $lastUpdate = \Illuminate\Support\Facades\Cache::get('inventory_last_updated', now()->timestamp);
        
        // Membuat kunci memori (Cache Key) yang spesifik untuk user ini dan grafik periode yang dipilihnya
        // Supaya dashboard super cepat saat direfresh tanpa membebani database
        $cacheKey = "operator_dashboard_{$userId}_{$lastUpdate}_".request('trend_period', '6_months');

        /** @return array<string, mixed> */
        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($userId): array {
            
            // --- Menarik maksimal 3 barang teratas yang sedang dipinjam oleh user ini ---
            $activeBorrowingsList = \App\Models\Borrowing::with(['sparepart'])
                ->where('user_id', $userId)
                ->where(function ($query) {
                    $query->where('status', 'borrowed')
                        ->orWhere('remaining_quantity', '>', 0);
                })
                ->latest('borrowed_at') // Urutkan dari barang yang paling baru dipinjam
                ->take(3)
                ->get()
                ->map(function (\App\Models\Borrowing $borrowing) {
                    // Merapikan data mentah menjadi bentuk array bersih untuk ditampilkan di HTML
                    return [
                        'id' => $borrowing->id,
                        'sparepart_id' => $borrowing->sparepart_id,
                        'sparepart_uuid' => $borrowing->sparepart->uuid ?? null,
                        'sparepart_name' => $borrowing->sparepart->name ?? 'Unknown',
                        'remaining_quantity' => $borrowing->remaining_quantity,
                        'borrowed_at_formatted' => $borrowing->borrowed_at ? $borrowing->borrowed_at->format('d M Y') : '-',
                        'expected_return_at_formatted' => $borrowing->expected_return_at ? $borrowing->expected_return_at->format('d M Y') : '-',
                        'is_overdue' => $borrowing->isOverdue(), // Mengecek keterlambatan
                    ];
                });

            // Menghitung angka pasti jumlah keseluruhan barang yang sedang dipinjam
            $activeBorrowingsCount = \App\Models\Borrowing::where('user_id', $userId)
                ->where(function ($query) {
                    $query->where('status', 'borrowed')
                        ->orWhere('remaining_quantity', '>', 0);
                })->count();

            // --- Menarik maksimal 3 pengajuan penambahan stok (Stock Request) yang belum di-Acc Admin ---
            $pendingRequestsList = \App\Models\StockLog::where('user_id', $userId)
                ->where('status', 'pending')
                ->with('sparepart')
                ->latest()
                ->take(3)
                ->get()
                ->map(function (\App\Models\StockLog $request) {
                    return [
                        'id' => $request->id,
                        'sparepart_id' => $request->sparepart_id,
                        'sparepart_uuid' => $request->sparepart->uuid ?? null,
                        'sparepart_name' => $request->sparepart->name ?? 'Unknown',
                        'type' => $request->type,
                        'quantity' => $request->quantity,
                        'unit' => $request->sparepart->unit ?? 'Pcs',
                        'created_at_formatted' => $request->created_at ? $request->created_at->format('d M Y H:i') : '-',
                    ];
                });

            // Menghitung angka total dari seluruh permintaan barang yang berstatus 'Pending'
            $pendingRequestsCount = \App\Models\StockLog::where('user_id', $userId)
                ->where('status', 'pending')
                ->count();

            // --- Menarik 3 Barang Favorit (Top Picks) berdasarkan yang paling sering dipinjam oleh user ---
            $topPicks = \App\Models\Borrowing::where('user_id', $userId)
                ->select('sparepart_id', \Illuminate\Support\Facades\DB::raw('count(*) as total_borrows'))
                ->groupBy('sparepart_id')
                ->orderByDesc('total_borrows') // Urutkan dengan angka peminjaman tertinggi
                ->with(['sparepart', 'sparepart.category'])
                ->take(3)
                ->get()
                ->map(function (\App\Models\Borrowing $pick) {
                    return [
                        'sparepart_id' => $pick->sparepart_id,
                        'sparepart_uuid' => $pick->sparepart->uuid ?? null,
                        'sparepart_name' => $pick->sparepart->name ?? 'Unknown',
                        'category_name' => $pick->sparepart->category->name ?? '-',
                        'total_borrows' => $pick->total_borrows,
                        'image_url' => $pick->sparepart->image ? \Illuminate\Support\Facades\Storage::url($pick->sparepart->image) : null,
                    ];
                });

            // --- Mencatat daftar rekam jejak aktivitas terakhir user di aplikasi (Audit Trail) ---
            $activityLogs = \App\Models\ActivityLog::where('user_id', $userId)
                ->latest()
                ->take(5)
                ->get()
                ->map(function (\App\Models\ActivityLog $log) {
                    return [
                        'action' => $log->action,
                        'action_lower' => strtolower($log->action),
                        'details' => strip_tags($log->details), // Buang karakter HTML (<p> <b>) agar tidak bocor
                        'created_at_diff' => $log->created_at ? $log->created_at->diffForHumans() : '-', // Format "5 menit yang lalu"
                    ];
                });

            // ================= DATA PERSIAPAN GRAFIK (CHARTS) =================

            // Menentukan panjang grafik yang diinginkan (default-nya 6 bulan)
            $trendPeriod = request('trend_period', '6_months');
            
            // Pengaturan interval tanggal/bulan
            $trendConfigs = [
                '7_days' => ['count' => 7, 'unit' => 'days', 'format' => 'Y-m-d', 'label' => 'd M', 'groupBy' => 'day'],
                '30_days' => ['count' => 30, 'unit' => 'days', 'format' => 'Y-m-d', 'label' => 'd M', 'groupBy' => 'day'],
                '1_year' => ['count' => 12, 'unit' => 'months', 'format' => 'Y-m', 'label' => 'M y', 'groupBy' => 'month'],
                '6_months' => ['count' => 6, 'unit' => 'months', 'format' => 'Y-m', 'label' => 'M y', 'groupBy' => 'month'],
            ];

            // Menarik referensi konfigurasi dan menentukan awal pencarian dari kalender
            $config = $trendConfigs[$trendPeriod] ?? $trendConfigs['6_months'];
            $startDate = now()->{'sub'.ucfirst($config['unit'])}($config['count'] - 1)->startOfDay();

            // Tarik seluruh data mentah transaksi dari database sejak start date
            $trendRawData = \App\Models\Borrowing::where('user_id', $userId)
                ->where('borrowed_at', '>=', $startDate)
                ->select('borrowed_at', 'expected_return_at', 'returned_at')
                ->get();

            // Mengelompokkannya dalam tumpukan per Hari/Bulan
            $groupedTrend = $trendRawData->groupBy(fn ($item) => $item->borrowed_at->format($config['format']));

            // Menyusun hasil data murni yang siap disuguhkan ke library grafik Javascript
            $borrowingTrend = collect(range($config['count'] - 1, 0))->map(function ($offset) use ($config, $groupedTrend) {
                $date = now()->{'sub'.ucfirst($config['unit'])}($offset);
                $key = $date->format($config['format']);
                // Jika di hari itu ada transaksi pinjam, hitung. Kalau tidak, anggap 0.
                $count = $groupedTrend->has($key) ? count($groupedTrend->get($key)) : 0;

                return [
                    'period' => $date->translatedFormat($config['label']), // Contoh output teks: "12 Ags"
                    'count' => $count,
                ];
            });

            // Menghitung angka status laporan (Request Barang) untuk disajikan di grafik Pie (lingkaran)
            $stockRequestStats = \App\Models\StockLog::where('user_id', $userId)
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            $stockChartData = [
                'pending' => $stockRequestStats['pending'] ?? 0,
                'approved' => $stockRequestStats['approved'] ?? 0,
                'rejected' => $stockRequestStats['rejected'] ?? 0,
            ];

            // Menghitung angka Nilai Kedisiplinan / Kepercayaan (Trust Score)
            // Sistem akan menilai apakah user ini hobi telat mengembalikan barang atau selalu on-time
            $stats = \App\Models\Borrowing::where('user_id', $userId)
                ->selectRaw('COUNT(*) as total, 
                    SUM(CASE WHEN returned_at IS NOT NULL AND returned_at > expected_return_at THEN 1 ELSE 0 END) as returned_late,
                    SUM(CASE WHEN returned_at IS NULL AND expected_return_at < ? THEN 1 ELSE 0 END) as active_overdue', [now()])
                ->first();

            $totalEvaluated = $stats->total;
            
            // Total poin dosa (jumlah telat mengembalikan + yang sekarang masih dipinjam tapi sudah lewat jatuh tempo)
            $totalLate = $stats->returned_late + $stats->active_overdue;
            
            // Jika user belum pernah meminjam sama sekali, kita beri modal angka 100 langsung.
            $trustScore = $totalEvaluated > 0 ? round((($totalEvaluated - $totalLate) / $totalEvaluated) * 100) : 100;

            // Memasukkan seluruh perhitungan ke dalam satu kantong (Array) 'result'
            // Menggunakan penugasan satu per satu untuk mencegah Intelephense (PHP6613) kebingungan membaca tipe data
            /** @var array<string, mixed> $result */
            $result = [];
            $result['activeBorrowingsCount'] = $activeBorrowingsCount;
            $result['pendingRequestsCount'] = $pendingRequestsCount;
            $result['activeBorrowingsList'] = $activeBorrowingsList;
            $result['pendingRequestsList'] = $pendingRequestsList;
            $result['activityLogs'] = $activityLogs;
            $result['borrowingTrend'] = $borrowingTrend;
            $result['stockChartData'] = $stockChartData;
            $result['topPicks'] = $topPicks;
            $result['trustScore'] = $trustScore;

            return $result;
        });

        $data['trendPeriod'] = request('trend_period', '6_months');

        // Jika fungsi dipanggil dari background script (AJAX API), keluarkan versi JSON
        if (request()->wantsJson()) {
            return response()->json($data);
        }

        // Tampilkan halaman HTML dashboard dengan menyuapkan bungkusan data lengkap
        return view('dashboard.operator', $data);
    }
}
