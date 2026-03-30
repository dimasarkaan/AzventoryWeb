<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class OperatorDashboardController extends Controller
{
    /**
     * Menampilkan dashboard untuk Operator.
     */
    public function index()
    {
        $userId = auth()->id();
        $lastUpdate = \Illuminate\Support\Facades\Cache::get('inventory_last_updated', now()->timestamp);
        $cacheKey = "operator_dashboard_{$userId}_{$lastUpdate}_".request('trend_period', '6_months');

        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($userId) {
            $activeBorrowingsList = \App\Models\Borrowing::with(['sparepart'])
                ->where('user_id', $userId)
                ->where(function ($query) {
                    $query->where('status', 'borrowed')
                        ->orWhere('remaining_quantity', '>', 0);
                })
                ->latest('borrowed_at')
                ->take(5)
                ->get()
                ->map(function ($borrowing) {
                    return [
                        'id' => $borrowing->id,
                        'sparepart_id' => $borrowing->sparepart_id,
                        'sparepart_name' => $borrowing->sparepart->name ?? 'Unknown',
                        'remaining_quantity' => $borrowing->remaining_quantity,
                        'borrowed_at_formatted' => $borrowing->borrowed_at ? $borrowing->borrowed_at->format('d M Y') : '-',
                        'expected_return_at_formatted' => $borrowing->expected_return_at ? $borrowing->expected_return_at->format('d M Y') : '-',
                        'is_overdue' => $borrowing->isOverdue(),
                    ];
                });

            $activeBorrowingsCount = \App\Models\Borrowing::where('user_id', $userId)
                ->where(function ($query) {
                    $query->where('status', 'borrowed')
                        ->orWhere('remaining_quantity', '>', 0);
                })->count();

            $pendingRequestsList = \App\Models\StockLog::where('user_id', $userId)
                ->where('status', 'pending')
                ->with('sparepart')
                ->latest()
                ->get()
                ->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'sparepart_id' => $request->sparepart_id,
                        'sparepart_name' => $request->sparepart->name ?? 'Unknown',
                        'type' => $request->type,
                        'quantity' => $request->quantity,
                        'unit' => $request->sparepart->unit ?? 'Pcs',
                        'created_at_formatted' => $request->created_at ? $request->created_at->format('d M Y H:i') : '-',
                    ];
                });

            $pendingRequestsCount = \App\Models\StockLog::where('user_id', $userId)
                ->where('status', 'pending')
                ->count();

            $topPicks = \App\Models\Borrowing::where('user_id', $userId)
                ->select('sparepart_id', \Illuminate\Support\Facades\DB::raw('count(*) as total_borrows'))
                ->groupBy('sparepart_id')
                ->orderByDesc('total_borrows')
                ->with('sparepart')
                ->take(3)
                ->get()
                ->map(function ($pick) {
                    return [
                        'sparepart_id' => $pick->sparepart_id,
                        'sparepart_name' => $pick->sparepart->name ?? 'Unknown',
                        'category_name' => $pick->sparepart->category->name ?? '-',
                        'total_borrows' => $pick->total_borrows,
                        'image_url' => $pick->sparepart->image ? \Illuminate\Support\Facades\Storage::url($pick->sparepart->image) : null,
                    ];
                });

            $activityLogs = \App\Models\ActivityLog::where('user_id', $userId)
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($log) {
                    return [
                        'action' => $log->action,
                        'action_lower' => strtolower($log->action),
                        'details' => strip_tags($log->details),
                        'created_at_diff' => $log->created_at ? $log->created_at->diffForHumans() : '-',
                    ];
                });

            // Data for Charts
            $trendPeriod = request('trend_period', '6_months');
            $trendConfigs = [
                '7_days' => ['count' => 7, 'unit' => 'days', 'format' => 'Y-m-d', 'label' => 'd M', 'groupBy' => 'day'],
                '30_days' => ['count' => 30, 'unit' => 'days', 'format' => 'Y-m-d', 'label' => 'd M', 'groupBy' => 'day'],
                '1_year' => ['count' => 12, 'unit' => 'months', 'format' => 'Y-m', 'label' => 'M y', 'groupBy' => 'month'],
                '6_months' => ['count' => 6, 'unit' => 'months', 'format' => 'Y-m', 'label' => 'M y', 'groupBy' => 'month'],
            ];

            $config = $trendConfigs[$trendPeriod] ?? $trendConfigs['6_months'];
            $startDate = now()->{'sub'.ucfirst($config['unit'])}($config['count'] - 1)->startOfDay();

            $trendRawData = \App\Models\Borrowing::where('user_id', $userId)
                ->where('borrowed_at', '>=', $startDate)
                ->select('borrowed_at', 'expected_return_at', 'returned_at')
                ->get();

            $groupedTrend = $trendRawData->groupBy(fn ($item) => $item->borrowed_at->format($config['format']));

            $borrowingTrend = collect(range($config['count'] - 1, 0))->map(function ($offset) use ($config, $groupedTrend) {
                $date = now()->{'sub'.ucfirst($config['unit'])}($offset);
                $key = $date->format($config['format']);
                $count = $groupedTrend->has($key) ? $groupedTrend->get($key)->count() : 0;

                return [
                    'period' => $date->translatedFormat($config['label']),
                    'count' => $count,
                ];
            });

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

            $stats = \App\Models\Borrowing::where('user_id', $userId)
                ->selectRaw('COUNT(*) as total, 
                    SUM(CASE WHEN returned_at IS NOT NULL AND returned_at > expected_return_at THEN 1 ELSE 0 END) as returned_late,
                    SUM(CASE WHEN returned_at IS NULL AND expected_return_at < ? THEN 1 ELSE 0 END) as active_overdue', [now()])
                ->first();

            $totalEvaluated = $stats->total;
            $totalLate = $stats->returned_late + $stats->active_overdue;
            $trustScore = $totalEvaluated > 0 ? round((($totalEvaluated - $totalLate) / $totalEvaluated) * 100) : 100;

            return compact(
                'activeBorrowingsCount',
                'pendingRequestsCount',
                'activeBorrowingsList',
                'pendingRequestsList',
                'activityLogs',
                'borrowingTrend',
                'stockChartData',
                'topPicks',
                'trustScore'
            );
        });

        $data['trendPeriod'] = request('trend_period', '6_months');

        if (request()->wantsJson()) {
            return response()->json($data);
        }

        return view('dashboard.operator', $data);
    }
}
