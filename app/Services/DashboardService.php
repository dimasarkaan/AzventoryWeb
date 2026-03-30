<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Borrowing;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Location;
use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * DashboardService menyediakan logika agregasi data untuk ringkasan dan statistik dashboard.
 */
class DashboardService
{
    /**
     * Mengurai rentang tanggal dari input periode (today, this_week, etc) atau custom range.
     */
    public function getDateRange(?string $period, ?string $year, ?string $month, ?string $startDate = null, ?string $endDate = null): array
    {
        // Default period is now 'this_month' (monthly overview)
        $period = ($period && $period !== '') ? $period : 'this_month';
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        try {
            if ($startDate && $endDate) {
                $start = Carbon::parse($startDate)->startOfDay();
                $end = Carbon::parse($endDate)->endOfDay();
                $period = 'custom';
            } elseif ($year && is_numeric($year)) {
                if ($month && $month !== 'all' && is_numeric($month) && $month >= 1 && $month <= 12) {
                    $start = Carbon::create($year, $month, 1)->startOfMonth();
                    $end = (clone $start)->endOfMonth();
                    $period = 'custom';
                } else {
                    $start = Carbon::create($year, 1, 1)->startOfYear();
                    $end = (clone $start)->endOfYear();
                    $period = 'custom_year';
                }
            } else {
                switch ($period) {
                    case 'today':
                        $start = Carbon::today();
                        $end = Carbon::tomorrow();
                        break;
                    case 'this_week':
                        $start = Carbon::now()->startOfWeek();
                        $end = Carbon::now()->endOfWeek();
                        break;
                    case 'this_year':
                        $start = Carbon::now()->startOfYear();
                        $end = Carbon::now()->endOfYear();
                        break;
                    case 'this_month':
                    default:
                        $start = Carbon::now()->startOfMonth();
                        $end = Carbon::now()->endOfMonth();
                        $period = 'this_month';
                        break;
                }
            }
        } catch (\Exception $e) {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            $period = 'this_month';
        }

        return [$start, $end, $period];
    }

    /**
     * Mengambil ringkasan statistik stok barang saat ini.
     */
    public function getStockSnapshots(): array
    {
        $lastUpdate = Cache::get('inventory_last_updated', now()->timestamp);
        $cacheKey = "stock_snapshots_{$lastUpdate}";

        return Cache::remember($cacheKey, 3600, function () {
            return [
                'totalSpareparts' => Sparepart::count(),
                'totalStock' => Sparepart::sum('stock'),
                'totalCategories' => Category::count(),
                'totalBrands' => Brand::count(),
                'totalLocations' => Location::count(),
                'pendingApprovalsCount' => StockLog::where('status', 'pending')->count(),
                'lowStockItems' => Sparepart::where('minimum_stock', '>', 0)
                    ->whereColumn('stock', '<=', 'minimum_stock')
                    ->where('condition', 'Baik')
                    ->take(5)->get(),
            ];
        });
    }

    /**
     * Mengambil statistik pergerakan stok dengan perbandingan persentase periode sebelumnya.
     */
    public function getStockMovements(Carbon $start, Carbon $end): array
    {
        $days = $start->diffInDays($end);
        $isWeekly = $days > 60; // Gunakan agregasi mingguan jika rentang waktu > 2 bulan

        $currentData = $this->fetchMovementStats($start, $end, $isWeekly);

        $prevStart = (clone $start)->subDays($days + 1);
        $prevEnd = (clone $start);
        $prevDataTotals = $this->fetchMovementTotals($prevStart, $prevEnd);

        $currentTotals = [
            'masuk' => array_sum($currentData['masuk']),
            'keluar' => array_sum($currentData['keluar']),
            'net' => array_sum($currentData['masuk']) - array_sum($currentData['keluar']),
        ];

        $comparison = [
            'masuk_pct' => $this->calculatePercentageChange($prevDataTotals['masuk'], $currentTotals['masuk']),
            'keluar_pct' => $this->calculatePercentageChange($prevDataTotals['keluar'], $currentTotals['keluar']),
            'net_pct' => $this->calculatePercentageChange($prevDataTotals['net'], $currentTotals['net']),
        ];

        return array_merge($currentData, ['comparison' => $comparison]);
    }

    private function fetchMovementStats(Carbon $start, Carbon $end, bool $isWeekly): array
    {
        // Optimasi: Satu query tunggal untuk seluruh periode agar efisien
        $allLogs = StockLog::where('status', 'approved')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as day, type, SUM(quantity) as total')
            ->groupBy('day', 'type')
            ->get()
            ->groupBy('day');

        $labels = [];
        $masuk = [];
        $keluar = [];

        $current = clone $start;

        if ($isWeekly) {
            while ($current->lt($end)) {
                $weekEnd = (clone $current)->addDays(6);
                if ($weekEnd->gt($end)) {
                    $weekEnd = clone $end;
                }

                $labels[] = $current->format('d M').' - '.$weekEnd->format('d M');

                $weekMasuk = 0.0;
                $weekKeluar = 0.0;

                $dayCursor = clone $current;
                while ($dayCursor->lte($weekEnd)) {
                    $dayStr = $dayCursor->format('Y-m-d');
                    $dayLogs = $allLogs->get($dayStr, collect());

                    $weekMasuk += (float) ($dayLogs->where('type', 'masuk')->first()?->total ?? 0);
                    $weekKeluar += (float) ($dayLogs->where('type', 'keluar')->first()?->total ?? 0);

                    $dayCursor->addDay();
                }

                $masuk[] = $weekMasuk;
                $keluar[] = $weekKeluar;

                $current->addDays(7);
            }
        } else {
            while ($current->lt($end)) {
                $dateStr = $current->format('Y-m-d');
                $labels[] = $current->format('d M');

                $dayLogs = $allLogs->get($dateStr, collect());
                $masuk[] = (float) ($dayLogs->where('type', 'masuk')->first()?->total ?? 0);
                $keluar[] = (float) ($dayLogs->where('type', 'keluar')->first()?->total ?? 0);

                $current->addDay();
            }
        }

        return compact('labels', 'masuk', 'keluar');
    }

    private function fetchMovementTotals(Carbon $start, Carbon $end): array
    {
        $totals = StockLog::where('status', 'approved')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('type, SUM(quantity) as total')
            ->groupBy('type')
            ->get();

        $masuk = (float) ($totals->where('type', 'masuk')->first()->total ?? 0);
        $keluar = (float) ($totals->where('type', 'keluar')->first()->total ?? 0);

        return [
            'masuk' => $masuk,
            'keluar' => $keluar,
            'net' => $masuk - $keluar,
        ];
    }

    private function calculatePercentageChange(float $old, float $new): ?float
    {
        if ($old == 0) {
            return $new > 0 ? 100 : ($new < 0 ? -100 : 0);
        }

        return round((($new - $old) / abs($old)) * 100, 1);
    }

    /**
     * Mendapatkan daftar barang dengan pergerakan tertinggi (Top Items).
     */
    public function getTopItems(Carbon $start, Carbon $end, string $type): Collection
    {
        return StockLog::join('spareparts', 'stock_logs.sparepart_id', '=', 'spareparts.id')
            ->where('stock_logs.status', 'approved')
            ->where('stock_logs.type', $type)
            ->whereBetween('stock_logs.created_at', [$start, $end])
            ->selectRaw('spareparts.name as sparepart_name, stock_logs.sparepart_id, SUM(stock_logs.quantity) as total_qty')
            ->groupBy('stock_logs.sparepart_id', 'spareparts.name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();
    }

    /**
     * Mendapatkan daftar barang yang tidak terjual/terpakai dalam kurun waktu lama (Dead Stock).
     */
    public function getDeadStock(Carbon $start, Carbon $end, string $period): Collection
    {
        $deadStockStart = $start;
        if (in_array($period, ['today', 'this_week', 'this_month'])) {
            $deadStockStart = Carbon::now()->subDays(90);
        }

        $activeItemIds = StockLog::where('type', 'keluar')
            ->where('status', 'approved')
            ->whereBetween('created_at', [$deadStockStart, $end])
            ->pluck('sparepart_id')
            ->unique();

        return Sparepart::where('stock', '>', 0)
            ->whereNotIn('id', $activeItemIds)
            ->take(5)
            ->get();
    }

    /**
     * Mendapatkan leaderboard pengguna berdasarkan jumlah aktivitas penyesuaian stok.
     */
    public function getUserLeaderboard(Carbon $start, Carbon $end): Collection
    {
        return StockLog::select('user_id', DB::raw('count(*) as total_actions'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('user_id')
            ->orderByDesc('total_actions')
            ->limit(5)
            ->with('user')
            ->get();
    }

    /**
     * Menghitung prediksi kebutuhan stok berdasarkan rata-rata penggunaan 3 bulan terakhir.
     */
    public function getForecasts(Collection $topExitedItems): array
    {
        $forecasts = [];
        if ($topExitedItems->isEmpty()) {
            return $forecasts;
        }

        $itemIds = $topExitedItems->pluck('sparepart_id')->toArray();
        $forecastStart = Carbon::now()->subMonths(3);
        $forecastEnd = Carbon::now();

        $usageStats = StockLog::whereIn('sparepart_id', $itemIds)
            ->where('type', 'keluar')
            ->where('status', 'approved')
            ->whereBetween('created_at', [$forecastStart, $forecastEnd])
            ->selectRaw('sparepart_id, SUM(quantity) as total_usage')
            ->groupBy('sparepart_id')
            ->pluck('total_usage', 'sparepart_id');

        $currentStocks = Sparepart::whereIn('id', $itemIds)->pluck('stock', 'id');

        foreach ($topExitedItems as $item) {
            $totalUsage = $usageStats[$item->sparepart_id] ?? 0;
            $avgMonthly = round($totalUsage / 3);

            $forecasts[] = [
                'name' => $item->sparepart_name,
                'current_stock' => $currentStocks[$item->sparepart_id] ?? 0,
                'avg_usage' => $avgMonthly,
                'predicted_need' => $avgMonthly,
            ];
        }

        return $forecasts;
    }

    /**
     * Mendapatkan statistik peminjaman (aktif & terlambat) berdasarkan hak akses pengguna.
     */
    public function getBorrowingStats(User $user): array
    {
        $borrowQuery = Borrowing::query();

        if ($user->role === \App\Enums\UserRole::ADMIN) {
            $operatorIds = User::where('role', \App\Enums\UserRole::OPERATOR)->pluck('id');
            $allowedUserIds = $operatorIds->push($user->id);
            $borrowQuery->whereIn('user_id', $allowedUserIds);
        } elseif ($user->role === \App\Enums\UserRole::OPERATOR) {
            $borrowQuery->where('user_id', $user->id);
        }

        $activeBorrowingsCount = (clone $borrowQuery)->where('status', 'borrowed')->count();

        $overdueBaseQuery = (clone $borrowQuery)->where('status', 'borrowed')
            ->where('expected_return_at', '<', now());

        $totalOverdueCount = $overdueBaseQuery->count();

        $overdueBorrowings = $overdueBaseQuery->with(['user', 'sparepart' => function ($q) {
            $q->withTrashed();
        }])
            ->orderBy('expected_return_at', 'asc')
            ->take(5)
            ->get();

        return compact('activeBorrowingsCount', 'totalOverdueCount', 'overdueBorrowings');
    }

    /**
     * Menampilkan daftar log aktivitas sistem terbaru.
     * Tidak dibatasi oleh filter tanggal agar selalu menampilkan update terkini.
     */
    public function getRecentActivities(?User $user = null, int $limit = 3): Collection
    {
        $query = ActivityLog::with('user');

        // Proteksi: Admin hanya boleh melihat log dari Admin & Operator (Hierarki privasi)
        if ($user && $user->role === \App\Enums\UserRole::ADMIN) {
            $allowedUserIds = User::whereIn('role', [
                \App\Enums\UserRole::ADMIN,
                \App\Enums\UserRole::OPERATOR,
            ])->pluck('id');

            $query->whereIn('user_id', $allowedUserIds);
        }

        return $query->latest()->take($limit)->get();
    }

    /**
     * Agregasi total stok berdasarkan atribut tertentu (kategori/lokasi).
     */
    public function getStockByAttribute(string $attribute): Collection
    {
        return Sparepart::select($attribute, DB::raw('sum(stock) as total'))
            ->groupBy($attribute)
            ->pluck('total', $attribute);
    }

    /**
     * Mendapatkan daftar tahun unik yang memiliki data riwayat untuk filter dinamis.
     */
    public function getAvailableYears(): array
    {
        return Cache::remember('dashboard_available_years', 3600, function () {
            $driver = DB::getDriverName();
            $yearExpression = $driver === 'sqlite' ? "strftime('%Y', created_at)" : 'YEAR(created_at)';

            $stockYears = DB::table('stock_logs')->selectRaw("DISTINCT {$yearExpression} as year")->pluck('year');
            $activityYears = DB::table('activity_logs')->selectRaw("DISTINCT {$yearExpression} as year")->pluck('year');

            $years = $stockYears->merge($activityYears)->unique()->sortDesc()->values()->toArray();

            return empty($years) ? [now()->year] : $years;
        });
    }
}
