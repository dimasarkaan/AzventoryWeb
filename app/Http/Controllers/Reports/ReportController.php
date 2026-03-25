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
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ], [
            'end_date.after_or_equal' => 'Tanggal akhir tidak boleh lebih awal dari tanggal mulai.',
        ]);

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

        // Generate Filename
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

        if ($format !== 'excel') {
            // Snapshot data for PDF
            $reportData = $this->reportService->getReportData($type, $location, $startDate, $endDate);
            
            // Jika data kecil (< 1000 item), langsung stream PDF (lebih handal di cPanel)
            if (count($reportData) <= 1000) {
                $this->logActivity('Laporan Diunduh', "Mengunduh PDF langsung tipe: {$type}");

                $pdf = app()->make('dompdf.wrapper')->loadView('reports.' . $type . '.pdf', [
                    'data' => $reportData,
                    'isPdf' => true,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'location' => $location,
                ]);

                return $pdf->download($filename . '.pdf');
            }

            // Jika data besar, gunakan antrean (Queue)
            \App\Jobs\GenerateReportJob::dispatch($request->user(), $reportData, $startDate, $endDate, $location, $type);

            $this->logActivity('Laporan Diproses', "Meminta antrean laporan PDF tipe: {$type}");

            $message = 'Laporan sedang diproses karena ukuran data yang besar. Silakan cek menu Notifikasi dalam beberapa saat untuk mengunduh file.';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'type' => 'info'
                ]);
            }

            return back()->with('info', $message);
        }

        // Jalur Export Excel (Response Langsung menggunakan Builder Streaming)
        $excelService = new \App\Services\ExcelExportService;

        // Jalur Export Excel (Response Langsung menggunakan Builder Streaming)
        return match ($type) {
            'inventory_list' => $excelService->exportInventoryList($query, $filename),
            'stock_mutation' => $excelService->exportStockMutation($query, $filename),
            'borrowing_history' => $excelService->exportBorrowingHistory($query, $filename),
            'low_stock' => $excelService->exportLowStock($query, $filename),
            default => back()->with('error', 'Tipe laporan tidak didukung untuk export Excel.'),
        };
    }
}
