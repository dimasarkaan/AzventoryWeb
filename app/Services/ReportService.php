<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\StockLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * ReportService mengelola pengambilan data untuk generator laporan (PDF/Excel).
 */
class ReportService
{
    /**
     * Mengambil dataset laporan berdasarkan tipe, lokasi, dan rentang tanggal.
     *
     * @return array ['data', 'title', 'view']
     */
    public function getReportData($type, $location, $startDate, $endDate)
    {
        $queryResult = $this->getReportQuery($type, $location, $startDate, $endDate);

        if (! $queryResult['query']) {
            return [
                'data' => collect(),
                'title' => $queryResult['title'],
                'view' => $queryResult['view'],
            ];
        }

        return [
            'data' => $queryResult['query']->get(),
            'title' => $queryResult['title'],
            'view' => $queryResult['view'],
        ];
    }

    /**
     * Mendapatkan query builder untuk laporan (mendukung streaming data/lazy loading).
     */
    public function getReportQuery($type, $location, $startDate, $endDate)
    {
        $query = null;
        $title = 'Laporan';
        $view = '';

        if ($type == 'inventory_list') {
            $query = Sparepart::orderBy('name');
            if ($location !== 'all' && $location) {
                $query->where('location', $location);
            }
            $title = 'Laporan Data Inventaris Saat Ini';
            $view = 'reports.pdf_inventory_list';

        } elseif ($type == 'stock_mutation') {
            $query = StockLog::with(['sparepart', 'user']);
            $this->applyDateRange($query, 'created_at', $startDate, $endDate);

            if ($location !== 'all' && $location) {
                $query->whereHas('sparepart', function ($q) use ($location) {
                    $q->where('location', $location);
                });
            }
            $query->latest();
            $title = 'Laporan Riwayat Stok / Mutasi';
            $view = 'reports.pdf_stock_mutation';

        } elseif ($type == 'borrowing_history') {
            $query = Borrowing::with(['sparepart', 'user'])->withSum('returns', 'quantity');
            $this->applyDateRange($query, 'borrowed_at', $startDate, $endDate);

            $query->latest();
            $title = 'Laporan Riwayat Peminjaman';
            $view = 'reports.pdf_borrowing_history';

        } elseif ($type == 'low_stock') {
            $query = Sparepart::where('minimum_stock', '>', 0)
                ->whereColumn('stock', '<=', 'minimum_stock')
                ->orderBy('stock', 'asc');
            if ($location !== 'all' && $location) {
                $query->where('location', $location);
            }
            $title = 'Laporan Stok Menipis';
            $view = 'reports.pdf_low_stock';
        }

        return [
            'query' => $query,
            'title' => $title,
            'view' => $view,
        ];
    }

    /**
     * Mengonversi string periode menjadi rentang objek Carbon.
     */
    public function resolveDateRange($period, $customStart = null, $customEnd = null)
    {
        $startDate = null;
        $endDate = null;

        if ($period == 'custom') {
            $startDate = $customStart ? Carbon::parse($customStart)->startOfDay() : null;
            $endDate = $customEnd ? Carbon::parse($customEnd)->endOfDay() : null;
        } elseif ($period == 'this_month') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        } elseif ($period == 'last_month') {
            $startDate = Carbon::now()->subMonth()->startOfMonth();
            $endDate = Carbon::now()->subMonth()->endOfMonth();
        } elseif ($period == 'this_year') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
        } elseif ($period == 'last_year') {
            $startDate = Carbon::now()->subYear()->startOfYear();
            $endDate = Carbon::now()->subYear()->endOfYear();
        }

        return [$startDate, $endDate];
    }

    /**
     * Helper untuk menerapkan filter rentang tanggal pada query builder.
     */
    private function applyDateRange(Builder $query, $column, $start, $end)
    {
        if ($start && $end) {
            $query->whereBetween($column, [$start, $end]);
        }
    }
}
