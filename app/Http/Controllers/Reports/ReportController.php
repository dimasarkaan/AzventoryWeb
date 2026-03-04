<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Traits\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request; // Added this line

class ReportController extends Controller
{
    use ActivityLogger;

    protected $reportService;

    protected $inventoryService;

    public function __construct(ReportService $reportService, \App\Services\InventoryService $inventoryService)
    {
        $this->reportService = $reportService;
        $this->inventoryService = $inventoryService;
    }

    /**
     * Menampilkan halaman indeks pelaporan.
     */
    public function index()
    {
        $options = $this->inventoryService->getDropdownOptions();
        $locations = $options['locations'];

        return view('reports.index', compact('locations'));
    }

    /**
     * Memproses permintaan unduhan laporan.
     * Untuk PDF, pembuatan file didelegasikan ke Queue Job untuk efisiensi server.
     */
    public function download(Request $request)
    {
        $type = $request->input('report_type', 'inventory');
        $period = $request->input('period', 'all');
        $format = $request->input('export_format', 'pdf');

        $location = $request->input('location', 'all');

        [$startDate, $endDate] = $this->reportService->resolveDateRange(
            $period,
            $request->input('start_date'),
            $request->input('end_date')
        );

        // Fetch query for non-PDF (Excel usually)
        $reportQuery = $this->reportService->getReportQuery($type, $location, $startDate, $endDate);
        $query = $reportQuery['query'];

        if (! $query) {
            return back()->with('error', 'Tipe laporan tidak ditemukan atau tidak valid.');
        }

        if ($format !== 'excel') {
            // Snapshot data for PDF Queue (To ensure consistent data since it's processed later)
            $reportData = $this->reportService->getReportData($type, $location, $startDate, $endDate);

            \App\Jobs\GenerateReportJob::dispatch($request->user(), $reportData, $startDate, $endDate, $location, $type);

            $this->logActivity('Laporan Diproses', "Meminta antrean laporan PDF tipe: {$type}");

            return back()->with('success', 'Laporan sedang memproses. Anda akan menerima notifikasi saat laporan siap diunduh.');
        }

        // Jalur Export Excel (Response Langsung menggunakan Builder Streaming)
        $prefix = match ($type) {
            'inventory_list' => 'LaporanInventaris',
            'stock_mutation' => 'LaporanMutasi',
            'borrowing_history' => 'LaporanPeminjaman',
            'low_stock' => 'LaporanStokMenipis',
            default => 'Laporan'
        };

        if ($startDate && $endDate) {
            $start = $startDate->format('d-m-Y');
            $end = $endDate->format('d-m-Y');
            $filename = "{$prefix}_{$start}sd{$end}";
        } else {
            $filename = "{$prefix}SemuaRiwayat_".now()->format('d-m-Y');
        }

        $this->logActivity('Laporan Diunduh', "Mengunduh file laporan Excel tipe: {$type}");

        $excelService = new \App\Services\ExcelExportService;

        // Pass the $query builder instead of $data collection for scalability
        return match ($type) {
            'inventory_list' => $excelService->exportInventoryList($query, $filename),
            'stock_mutation' => $excelService->exportStockMutation($query, $filename),
            'borrowing_history' => $excelService->exportBorrowingHistory($query, $filename),
            'low_stock' => $excelService->exportLowStock($query, $filename),
            default => back()->with('error', 'Tipe laporan tidak didukung untuk export Excel.'),
        };
    }
}
