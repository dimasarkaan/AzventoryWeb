<?php

namespace App\Services;

use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class ReportService
{
    // Dapatkan data laporan berdasarkan tipe dan filter.
    // Mengembalikan array dengan ['data', 'title', 'view'].
    public function getReportData($type, $location, $startDate, $endDate)
    {
        $data = collect();
        $title = 'Laporan';
        $view = '';

        if ($type == 'inventory_list') {
            $query = Sparepart::orderBy('name');
            if ($location !== 'all' && $location) {
                $query->where('location', $location);
            }
            $data = $query->get();
            $title = 'Laporan Data Inventaris Saat Ini';
            $view = 'reports.pdf_inventory_list';

        } elseif ($type == 'stock_mutation') {
            $query = StockLog::with(['sparepart', 'user']);
            $this->applyDateRange($query, 'created_at', $startDate, $endDate);
            
            if ($location !== 'all' && $location) {
                $query->whereHas('sparepart', function($q) use ($location) {
                    $q->where('location', $location);
                });
            }
            $data = $query->latest()->get();
            $title = 'Laporan Riwayat Stok / Mutasi';
            $view = 'reports.pdf_stock_mutation';

        } elseif ($type == 'borrowing_history') {
            $query = Borrowing::with(['sparepart', 'user']);
            $this->applyDateRange($query, 'borrowed_at', $startDate, $endDate);
            
            $data = $query->latest()->get();
            $title = 'Laporan Riwayat Peminjaman';
            $view = 'reports.pdf_borrowing_history';

        } elseif ($type == 'low_stock') {
            $query = Sparepart::whereColumn('stock', '<=', 'minimum_stock')->orderBy('stock', 'asc');
            if ($location !== 'all' && $location) {
                $query->where('location', $location);
            }
            $data = $query->get();
            $title = 'Laporan Stok Menipis';
            $view = 'reports.pdf_low_stock';
        }

        return compact('data', 'title', 'view');
    }

    // Urai rentang tanggal dari string periode.
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

    private function applyDateRange(Builder $query, $column, $start, $end)
    {
        if ($start && $end) {
            $query->whereBetween($column, [$start, $end]);
        }
    }
}
