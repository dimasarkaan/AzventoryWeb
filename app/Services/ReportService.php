<?php

namespace App\Services;

use App\Models\Borrowing;
use App\Models\Sparepart;
use App\Models\StockLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

// Service (Pekerja Keras) khusus untuk mengumpulkan data dari database dan merakitnya menjadi Laporan (PDF/Excel).
// Merupakan Otak di balik fitur "Cetak Laporan".
class ReportService
{
    // Tahap Eksekusi: Mengeksekusi pencarian data ke database dan menyerahkan hasil akhirnya (Siap Cetak)
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

    // Tahap Perencanaan: Menyusun strategi pencarian (Query Builder) tergantung jenis laporan apa yang diminta oleh pengguna
    public function getReportQuery($type, $location, $startDate, $endDate)
    {
        $query = null;
        $title = 'Laporan';
        $view = '';

        if ($type == 'inventory_list') {
            $query = Sparepart::with(['brand', 'category', 'location'])->orderBy('name');
            if ($location !== 'all' && $location) {
                $query->whereHas('location', function ($q) use ($location) {
                    $q->where('name', $location);
                });
            }
            $title = 'Laporan Data Inventaris Saat Ini';
            $view = 'reports.pdf_inventory_list';

        } elseif ($type == 'stock_mutation') {
            $query = StockLog::with(['sparepart.brand', 'sparepart.category', 'sparepart.location', 'user']);
            $this->applyDateRange($query, 'created_at', $startDate, $endDate);

            if ($location !== 'all' && $location) {
                $query->whereHas('sparepart', function ($q) use ($location) {
                    $q->whereHas('location', function ($lq) use ($location) {
                        $lq->where('name', $location);
                    });
                });
            }
            $query->latest();
            $title = 'Laporan Riwayat Stok / Mutasi';
            $view = 'reports.pdf_stock_mutation';

        } elseif ($type == 'borrowing_history') {
            $query = Borrowing::with(['sparepart.brand', 'sparepart.category', 'sparepart.location', 'user'])->withSum('returns', 'quantity');
            $this->applyDateRange($query, 'borrowed_at', $startDate, $endDate);

            $query->latest();
            $title = 'Laporan Riwayat Peminjaman';
            $view = 'reports.pdf_borrowing_history';

        } elseif ($type == 'low_stock') {
            $query = Sparepart::with(['brand', 'category', 'location'])
                ->where('minimum_stock', '>', 0)
                ->whereColumn('stock', '<=', 'minimum_stock')
                ->orderBy('stock', 'asc');
            if ($location !== 'all' && $location) {
                $query->whereHas('location', function ($q) use ($location) {
                    $q->where('name', $location);
                });
            }
            $title = 'Laporan Stok Menipis';
            $view = 'reports.pdf_low_stock';
        } elseif ($type == 'activity_log') {
            $query = \App\Models\ActivityLog::with('user');
            $this->applyDateRange($query, 'created_at', $startDate, $endDate);
            $query->latest();
            $title = 'Laporan Riwayat Aktivitas';
            $view = 'reports.pdf_activity_log';
        }

        return [
            'query' => $query,
            'title' => $title,
            'view' => $view,
        ];
    }

    // Menerjemahkan bahasa manusia (contoh: "Bulan Ini") menjadi bahasa kalender komputer (Tanggal awal - akhir)
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

    // Filter Tambahan: Membuang data yang terjadi di luar rentang tanggal yang diminta (Tanggal Mulai s.d Tanggal Selesai)
    private function applyDateRange(Builder $query, $column, $start, $end)
    {
        if ($start && $end) {
            $query->whereBetween($column, [$start, $end]);
        }
    }
}
