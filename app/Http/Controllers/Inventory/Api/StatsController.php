<?php

namespace App\Http\Controllers\Inventory\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Mendapatkan ringkasan statistik sistem.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Stock Snapshots (Total items, stock, low stock count, etc)
        $snapshots = $this->dashboardService->getStockSnapshots();

        // Borrowing Stats (Active, Overdue)
        $borrowingStats = $this->dashboardService->getBorrowingStats($user);

        return response()->json([
            'status' => 'success',
            'data' => [
                'inventory' => [
                    'total_items' => $snapshots['totalSpareparts'] ?? 0,
                    'total_stock' => (int) ($snapshots['totalStock'] ?? 0),
                    'low_stock_count' => count($snapshots['lowStockItems'] ?? []),
                ],
                'borrowing' => [
                    'active_count' => $borrowingStats['activeBorrowingsCount'] ?? 0,
                    'overdue_count' => $borrowingStats['totalOverdueCount'] ?? 0,
                ],
                'master_data' => [
                    'brands_count' => $snapshots['totalBrands'] ?? 0,
                    'categories_count' => $snapshots['totalCategories'] ?? 0,
                    'locations_count' => $snapshots['totalLocations'] ?? 0,
                ],
            ],
        ]);
    }
}
