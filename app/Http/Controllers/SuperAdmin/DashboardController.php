<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Sparepart;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Added DB facade
use Carbon\Carbon; // Added Carbon import

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Debug
        // dd('Hit SuperAdmin Dashboard');
        // Log::info('Dashboard Params:', $request->all());

        // 1. Determine Date Range
        $period = $request->input('period', 'today'); // Default
        $year = $request->input('year');
        $month = $request->input('month');
        
        // Default: Today
        $start = Carbon::today();
        $end = Carbon::tomorrow();

        if ($year) {
            // YEAR BASED FILTER
            if ($month && $month !== 'all') {
                // Specific Month in Year
                $start = Carbon::create($year, $month, 1)->startOfMonth();
                $end = Carbon::create($year, $month, 1)->endOfMonth();
                $period = 'custom'; // For UI active state
            } else {
                // Full Year
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

        // --- Existing Counts (Snapshot - not filtered by date usually, but can be if requested. For now keeping them as "Current State") ---
        $totalSpareparts = Sparepart::count();
        $totalStock = Sparepart::sum('stock');
        $totalCategories = Sparepart::distinct('category')->count('category');
        $totalLocations = Sparepart::distinct('location')->count('location');
        $lowStockItems = Sparepart::whereColumn('stock', '<=', 'minimum_stock')->take(5)->get();

        // --- New Analytics (Filtered by Date) ---
        
        // 1. Stock Movement Chart (Masuk vs Keluar over time)
        $movementStats = StockLog::where('status', 'approved')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as date, type, SUM(quantity) as total')
            ->groupBy('date', 'type')
            ->get();

        $dates = $movementStats->pluck('date')->unique()->sort()->values();
        $movementData = [
            'labels' => $dates->map(fn($d) => Carbon::parse($d)->format('d M'))->toArray(),
            'masuk' => [],
            'keluar' => []
        ];

        foreach ($dates as $date) {
            $movementData['masuk'][] = $movementStats->where('date', $date)->where('type', 'masuk')->first()->total ?? 0;
            $movementData['keluar'][] = $movementStats->where('date', $date)->where('type', 'keluar')->first()->total ?? 0;
        }

        // 2. Top Fast Moving Items (Most 'keluar')
        $topExited = StockLog::join('spareparts', 'stock_logs.sparepart_id', '=', 'spareparts.id')
            ->where('stock_logs.status', 'approved')
            ->where('stock_logs.type', 'keluar')
            ->whereBetween('stock_logs.created_at', [$start, $end])
            ->selectRaw('spareparts.name as sparepart_name, stock_logs.sparepart_id, SUM(stock_logs.quantity) as total_qty')
            ->groupBy('stock_logs.sparepart_id', 'spareparts.name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // 3. Top Incoming Items (Most 'masuk')
        $topEntered = StockLog::join('spareparts', 'stock_logs.sparepart_id', '=', 'spareparts.id')
            ->where('stock_logs.status', 'approved')
            ->where('stock_logs.type', 'masuk')
            ->whereBetween('stock_logs.created_at', [$start, $end])
            ->selectRaw('spareparts.name as sparepart_name, stock_logs.sparepart_id, SUM(stock_logs.quantity) as total_qty')
            ->groupBy('stock_logs.sparepart_id', 'spareparts.name') // standard group by name for display. Sparepart names should be unique essentially or it groups them, which is fine.
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // 4. Dead Stock / Slow Moving Items
        // Items with NO 'keluar' logs in the selected period (min stock > 0)
        $deadStockStart = $start;
        if ($period == 'today' || $period == 'this_week') {
             $deadStockStart = Carbon::now()->subDays(90); // Fallback for meaningful dead stock
        }

        $activeItemIds = StockLog::where('type', 'keluar')
            ->where('status', 'approved')
            ->whereBetween('created_at', [$deadStockStart, $end])
            ->pluck('sparepart_id')
            ->unique();
            
        $deadStockItems = Sparepart::where('stock', '>', 0)
            ->whereNotIn('id', $activeItemIds)
            ->take(5)
            ->get();

        // 5. User Activity Leaderboard
        $activeUsers = StockLog::select('user_id', DB::raw('count(*) as total_actions'))
            ->whereBetween('created_at', [$start, $end])
            ->with('user') // Eager load user relationship
            ->groupBy('user_id')
            ->orderByDesc('total_actions')
            ->limit(5)
            ->get();

        // 6. Simple Forecasting (Moving Average)
        // Predict next month's need for Top Exited items based on last 3 months average
        $forecasts = [];
        // Determine 3 months window for historical data
        $forecastStart = Carbon::now()->subMonths(3);
        $forecastEnd = Carbon::now();
        
        foreach ($topExited as $item) {
            $last3MonthsUsage = StockLog::where('sparepart_id', $item->sparepart_id)
                ->where('type', 'keluar')
                ->where('status', 'approved')
                ->whereBetween('created_at', [$forecastStart, $forecastEnd])
                ->sum('quantity');
            
            $avgMonthly = round($last3MonthsUsage / 3);
            $forecasts[] = [
                'name' => $item->sparepart_name,
                'current_stock' => Sparepart::find($item->sparepart_id)->stock ?? 0,
                'avg_usage' => $avgMonthly,
                'predicted_need' => $avgMonthly
            ];
        }


        // --- Existing Charts (Static / Snapshot based) ---
        // We might want to filter these by "updated_at" in the future, but usually "Stock by Location" is a current snapshot.
        $stockByCategory = Sparepart::select('category', DB::raw('sum(stock) as total'))
            ->groupBy('category')
            ->pluck('total', 'category');

        $stockByLocation = Sparepart::select('location', DB::raw('sum(stock) as total'))
            ->groupBy('location')
            ->pluck('total', 'location');

        // 7. Active Borrowings & Overdue Items (Role-Based)
        $user = auth()->user();
        $borrowQuery = \App\Models\Borrowing::query();

        if ($user->role === 'admin') {
            // Admin sees Own + Operators
            $operatorIds = \App\Models\User::where('role', 'operator')->pluck('id');
            $allowedUserIds = $operatorIds->push($user->id);
            $borrowQuery->whereIn('user_id', $allowedUserIds);
        } elseif ($user->role === 'operator' || $user->role === 'user') {
            // Operator/User sees Own only
            $borrowQuery->where('user_id', $user->id);
        }
        // SuperAdmin sees ALL (no filter needed)

        // Clone query for efficiency
        $activeQuery = clone $borrowQuery;
        $overdueQuery = clone $borrowQuery;

        // Active Borrowings Count
        $activeBorrowingsCount = $activeQuery->where('status', 'borrowed')->count();

        // Overdue Items (List top 5 + Count)
        $overdueBaseQuery = $overdueQuery->where('status', 'borrowed')
            ->where('expected_return_at', '<', now());
        
        $totalOverdueCount = $overdueBaseQuery->count();
        
        $overdueBorrowings = $overdueBaseQuery->with(['user', 'sparepart'])
            ->orderBy('expected_return_at', 'asc') // Most overdue first
            ->take(5)
            ->get();


        // New Dashboard Data
        $pendingApprovalsCount = StockLog::where('status', 'pending')->count();
        $lowStockItems = Sparepart::whereColumn('stock', '<=', 'minimum_stock')->take(5)->get();
        // Recent Activities - Filtered by Date now
        $recentActivities = ActivityLog::with('user')
            ->whereBetween('created_at', [$start, $end])
            ->latest()
            ->take(3) 
            ->get();

        return view('superadmin.dashboard', compact(
            'totalSpareparts',
            'totalStock',
            'totalCategories',
            'totalLocations',
            'stockByCategory',
            'stockByLocation',
            'pendingApprovalsCount',
            'lowStockItems',
            'recentActivities',
            'period',
            'start',
            'end',
            'movementData',
            'topExited',
            'topEntered',
            'deadStockItems',
            'activeUsers',
            'forecasts',
            'year',
            'month',
            'activeBorrowingsCount',
            'totalOverdueCount',
            'overdueBorrowings'
        ));
    }
}
