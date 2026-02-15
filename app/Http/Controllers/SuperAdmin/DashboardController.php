<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index(Request $request)
    {
        // 1. Determine Date Range
        [$start, $end, $period] = $this->dashboardService->getDateRange(
            $request->input('period'),
            $request->input('year'),
            $request->input('month')
        );

        $year = $request->input('year');
        $month = $request->input('month');
        $user = auth()->user();
        
        // Cache Key generation based on filters and user role (since borrowings depend on role)
        $cacheKey = 'dashboard_stats_' . $period . '_' . ($year ?? 'nay') . '_' . ($month ?? 'nam') . '_' . $user->id;
        
        // Remember for 10 minutes (600 seconds)
        // Note: You can adjust this time or make it configurable
        $data = Cache::remember($cacheKey, 600, function () use ($start, $end, $period, $user) {
            
            // --- Snapshots (Not filtered by date usually) ---
            $snapshots = $this->dashboardService->getStockSnapshots();
            
            // --- Filtered Analytics ---
            $movementData = $this->dashboardService->getStockMovements($start, $end);
            $topExited = $this->dashboardService->getTopItems($start, $end, 'keluar');
            $topEntered = $this->dashboardService->getTopItems($start, $end, 'masuk');
            $deadStockItems = $this->dashboardService->getDeadStock($start, $end, $period);
            $activeUsers = $this->dashboardService->getUserLeaderboard($start, $end);
            $forecasts = $this->dashboardService->getForecasts($topExited);
            
            // --- Charts ---
            $stockByCategory = $this->dashboardService->getStockByAttribute('category');
            $stockByLocation = $this->dashboardService->getStockByAttribute('location');
            
            // --- Borrowings (Role Based) ---
            $borrowingStats = $this->dashboardService->getBorrowingStats($user);
            
            // --- Recent Activities ---
            $recentActivities = $this->dashboardService->getRecentActivities($start, $end);

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
                ],
                $borrowingStats
            );
        });

        return view('superadmin.dashboard', array_merge($data, [
            'period' => $period,
            'start' => $start,
            'end' => $end,
            'year' => $year,
            'month' => $month,
        ]));
    }
}
