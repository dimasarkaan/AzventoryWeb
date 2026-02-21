<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OperatorDashboardController extends Controller
{
    /**
     * Menampilkan dashboard untuk Operator.
     */
    public function index()
    {
        $userId = auth()->id();
        
        $activeBorrowingsList = \App\Models\Borrowing::with(['sparepart'])
            ->where('user_id', $userId)
            ->where(function ($query) {
                $query->where('status', 'borrowed')
                      ->orWhere('remaining_quantity', '>', 0);
            })
            ->latest('borrowed_at')
            ->take(5)
            ->get();
            
        $activeBorrowingsCount = $activeBorrowingsList->count();

        $pendingRequestsList = \App\Models\StockLog::where('user_id', $userId)
            ->where('status', 'pending')
            ->with('sparepart')
            ->latest()
            ->get();
            
        $pendingRequestsCount = \App\Models\StockLog::where('user_id', $userId)
            ->where('status', 'pending')
            ->count();
            
        // 3. User's Top Picks (Most frequently borrowed items)
        $topPicks = \App\Models\Borrowing::where('user_id', $userId)
            ->select('sparepart_id', \Illuminate\Support\Facades\DB::raw('count(*) as total_borrows'))
            ->groupBy('sparepart_id')
            ->orderByDesc('total_borrows')
            ->with('sparepart')
            ->take(3)
            ->get();

        $activityLogs = \App\Models\ActivityLog::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        // Data for Charts
        // 1. Borrowing Trend (Dynamic Period)
        $trendPeriod = request('trend_period', '6_months');
        $borrowingTrend = collect();

        switch ($trendPeriod) {
            case '7_days':
                $borrowingTrend = collect(range(6, 0))->map(function ($days) use ($userId) {
                    $date = now()->subDays($days);
                    $count = \App\Models\Borrowing::where('user_id', $userId)
                        ->whereDate('borrowed_at', $date->format('Y-m-d'))
                        ->count();
                    return [
                        'period' => $date->translatedFormat('d M'),
                        'count' => $count
                    ];
                });
                break;
            case '30_days':
                $borrowingTrend = collect(range(29, 0))->map(function ($days) use ($userId) {
                    $date = now()->subDays($days);
                    $count = \App\Models\Borrowing::where('user_id', $userId)
                        ->whereDate('borrowed_at', $date->format('Y-m-d'))
                        ->count();
                    return [
                        'period' => $date->translatedFormat('d M'),
                        'count' => $count
                    ];
                });
                break;
            case '1_year':
                $borrowingTrend = collect(range(11, 0))->map(function ($months) use ($userId) {
                    $date = now()->subMonths($months);
                    $count = \App\Models\Borrowing::where('user_id', $userId)
                        ->whereYear('borrowed_at', $date->year)
                        ->whereMonth('borrowed_at', $date->month)
                        ->count();
                    return [
                        'period' => $date->translatedFormat('M y'),
                        'count' => $count
                    ];
                });
                break;
            case '6_months':
            default:
                $borrowingTrend = collect(range(5, 0))->map(function ($months) use ($userId) {
                    $date = now()->subMonths($months);
                    $count = \App\Models\Borrowing::where('user_id', $userId)
                        ->whereYear('borrowed_at', $date->year)
                        ->whereMonth('borrowed_at', $date->month)
                        ->count();
                    return [
                        'period' => $date->translatedFormat('M y'),
                        'count' => $count
                    ];
                });
                break;
        }

        // 2. Stock Request Status Distribution
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

        // 3. Trust Score Calculation
        $totalEvaluated = \App\Models\Borrowing::where('user_id', $userId)->count();
        
        $lateReturns = \App\Models\Borrowing::where('user_id', $userId)
            ->whereNotNull('returned_at')
            ->whereColumn('returned_at', '>', 'expected_return_at')
            ->count();
            
        $activeOverdue = \App\Models\Borrowing::where('user_id', $userId)
            ->whereNull('returned_at')
            ->where('expected_return_at', '<', now())
            ->count();
            
        $totalLate = $lateReturns + $activeOverdue;
        
        $trustScore = $totalEvaluated > 0 ? round((($totalEvaluated - $totalLate) / $totalEvaluated) * 100) : 100;

        return view('dashboard.operator', compact(
            'activeBorrowingsCount', 
            'pendingRequestsCount',
            'activeBorrowingsList',
            'pendingRequestsList',
            'activityLogs',
            'borrowingTrend',
            'stockChartData',
            'topPicks',
            'trustScore',
            'trendPeriod'
        ));
    }
}
