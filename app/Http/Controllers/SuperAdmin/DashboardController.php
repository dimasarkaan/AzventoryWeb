<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Sparepart;
use App\Models\StockLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Summary Cards Data
        $totalSpareparts = Sparepart::count();
        $totalStock = Sparepart::sum('stock');
        $totalCategories = Sparepart::distinct('category')->count('category');
        $totalLocations = Sparepart::distinct('location')->count('location');

        // Donut Chart: Stock by Category
        $stockByCategory = Sparepart::select('category')
            ->selectRaw('SUM(stock) as total_stock')
            ->groupBy('category')
            ->pluck('total_stock', 'category');

        // Bar Chart: Stock by Location
        $stockByLocation = Sparepart::select('location')
            ->selectRaw('SUM(stock) as total_stock')
            ->groupBy('location')
            ->pluck('total_stock', 'location');

        // New Dashboard Data
        $pendingApprovalsCount = StockLog::where('status', 'pending')->count();
        $lowStockItems = Sparepart::whereColumn('stock', '<=', 'minimum_stock')->get();
        $recentActivities = ActivityLog::with('user')->latest()->take(3)->get();

        return view('superadmin.dashboard', compact(
            'totalSpareparts',
            'totalStock',
            'totalCategories',
            'totalLocations',
            'stockByCategory',
            'stockByLocation',
            'pendingApprovalsCount',
            'lowStockItems',
            'recentActivities'
        ));
    }
}
