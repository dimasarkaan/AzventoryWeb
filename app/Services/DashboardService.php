<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\Borrowing;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DashboardService
{
    // Dapatkan rentang tanggal berdasarkan input request.
    public function getDateRange(?string $period, ?string $year, ?string $month): array
    {
        $period = $period ?? 'today';
        $start = Carbon::today();
        $end = Carbon::tomorrow();

        if ($year) {
            if ($month && $month !== 'all') {
                $start = Carbon::create($year, $month, 1)->startOfMonth();
                $end = Carbon::create($year, $month, 1)->endOfMonth();
                $period = 'custom';
            } else {
                $start = Carbon::create($year, 1, 1)->startOfYear();
                $end = Carbon::create($year, 1, 1)->endOfYear();
                $period = 'custom_year';
            }
        } elseif ($period == 'today') {
            $start = Carbon::today();
            $end = Carbon::tomorrow();
        } elseif ($period == 'this_week') {
            $start = Carbon::now()->startOfWeek();
            $end = Carbon::now()->endOfWeek();
        } elseif ($period == 'this_month') {
            $start = Carbon::now()->startOfMonth();
            $end = Carbon::now()->endOfMonth();
        } elseif ($period == 'this_year') {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::now()->endOfYear();
        }

        return [$start, $end, $period];
    }

    // Dapatkan ringkasan stok (Snapshot).
    public function getStockSnapshots(): array
    {
        return [
            'totalSpareparts' => Sparepart::count(),
            'totalStock' => Sparepart::sum('stock'),
            'totalCategories' => Sparepart::distinct('category')->count('category'),
            'totalLocations' => Sparepart::distinct('location')->count('location'),
            'pendingApprovalsCount' => StockLog::where('status', 'pending')->count(),
            'lowStockItems' => Sparepart::whereColumn('stock', '<=', 'minimum_stock')
                ->where('condition', 'Baik')
                ->take(5)->get(),
        ];
    }

    // Dapatkan data grafik pergerakan stok.
    public function getStockMovements(Carbon $start, Carbon $end): array
    {
        $movementStats = StockLog::where('status', 'approved')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, type, SUM(quantity) as total')
            ->groupBy('date', 'type')
            ->get();

        $dates = $movementStats->pluck('date')->unique()->sort()->values();
        $labels = $dates->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray();
        $masuk = [];
        $keluar = [];

        foreach ($dates as $date) {
            $masuk[] = $movementStats->where('date', $date)->where('type', 'masuk')->first()->total ?? 0;
            $keluar[] = $movementStats->where('date', $date)->where('type', 'keluar')->first()->total ?? 0;
        }

        return compact('labels', 'masuk', 'keluar');
    }

    // Dapatkan barang paling sering masuk/keluar.
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

    // Dapatkan barang 'Dead Stock' (tidak bergerak lama).
    public function getDeadStock(Carbon $start, Carbon $end, string $period): Collection
    {
        $deadStockStart = $start;
        if ($period == 'today' || $period == 'this_week') {
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

    // Dapatkan leaderboard aktivitas user.
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

    // Dapatkan prediksi kebutuhan stok (Forecast).
    public function getForecasts(Collection $topExitedItems): array
    {
        $forecasts = [];
        if ($topExitedItems->isEmpty()) {
            return $forecasts;
        }

        $itemIds = $topExitedItems->pluck('sparepart_id')->toArray();
        $forecastStart = Carbon::now()->subMonths(3);
        $forecastEnd = Carbon::now();

        // Optimized: Single query instead of loop
        $usageStats = StockLog::whereIn('sparepart_id', $itemIds)
            ->where('type', 'keluar')
            ->where('status', 'approved')
            ->whereBetween('created_at', [$forecastStart, $forecastEnd])
            ->selectRaw('sparepart_id, SUM(quantity) as total_usage')
            ->groupBy('sparepart_id')
            ->pluck('total_usage', 'sparepart_id');

        // Current stock mapping
        $currentStocks = Sparepart::whereIn('id', $itemIds)->pluck('stock', 'id');

        foreach ($topExitedItems as $item) {
            $totalUsage = $usageStats[$item->sparepart_id] ?? 0;
            $avgMonthly = round($totalUsage / 3);
            
            $forecasts[] = [
                'name' => $item->sparepart_name,
                'current_stock' => $currentStocks[$item->sparepart_id] ?? 0,
                'avg_usage' => $avgMonthly,
                'predicted_need' => $avgMonthly
            ];
        }

        return $forecasts;
    }

    // Dapatkan statistik peminjaman aktif & terlambat berdasarkan Role.
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

        $activeQuery = clone $borrowQuery;
        $overdueQuery = clone $borrowQuery;

        $activeBorrowingsCount = $activeQuery->where('status', 'borrowed')->count();

        $overdueBaseQuery = $overdueQuery->where('status', 'borrowed')
            ->where('expected_return_at', '<', now());
        
        $totalOverdueCount = $overdueBaseQuery->count();
        
        $overdueBorrowings = $overdueBaseQuery->with(['user', 'sparepart' => function($q) {
                $q->withTrashed();
            }])
            ->orderBy('expected_return_at', 'asc')
            ->take(5)
            ->get();

        return compact('activeBorrowingsCount', 'totalOverdueCount', 'overdueBorrowings');
    }

    /**
     * Dapatkan aktivitas terbaru berdasarkan periode.
     *
     * Jika user adalah Admin, filter hanya log dari user dengan role admin/operator
     * (bukan superadmin) untuk menjaga privasi hierarki.
     */
    public function getRecentActivities(Carbon $start, Carbon $end, ?User $user = null): Collection
    {
        $query = ActivityLog::with('user')
            ->whereBetween('created_at', [$start, $end]);

        // Jika Admin, batasi hanya aktivitas dari Admin & Operator (bukan Superadmin)
        if ($user && $user->role === \App\Enums\UserRole::ADMIN) {
            $allowedUserIds = User::whereIn('role', [
                \App\Enums\UserRole::ADMIN,
                \App\Enums\UserRole::OPERATOR,
            ])->pluck('id');

            $query->whereIn('user_id', $allowedUserIds);
        }

        return $query->latest()->take(4)->get();
    }
    
    public function getStockByAttribute(string $attribute): Collection
    {
         return Sparepart::select($attribute, DB::raw('sum(stock) as total'))
            ->groupBy($attribute)
            ->pluck('total', $attribute);
    }
}
