<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateReportJob;
use App\Notifications\ReportReadyNotification;
use App\Services\InventoryService;
use App\Services\ReportService;
use App\Traits\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    use ActivityLogger;

    protected $reportService;

    protected $inventoryService;

    public function __construct(ReportService $reportService, InventoryService $inventoryService)
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

            // Jika data kecil (<= 1000 item), langsung stream PDF (lebih handal di cPanel)
            if (count($reportData['data']) <= 1000) {
                $this->logActivity('Laporan Diunduh', "Mengunduh PDF langsung tipe: {$type}");

                $pdf = app()->make('dompdf.wrapper')->loadView($reportData['view'], [
                    'data' => $reportData['data'],
                    'title' => $reportData['title'],
                    'isPdf' => true,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'location' => $location,
                ]);

                // Tambahan: Simpan ke storage & Notifikasi agar ada History/Riwayat di lonceng (sesuai permintaan user)
                $pdfOutput = $pdf->output();
                $filenameWithExt = $filename.'.pdf';
                $path = 'reports/'.$filenameWithExt;
                Storage::disk('public')->put($path, $pdfOutput);

                // Kirim Notifikasi (History)
                $url = Storage::url($path);
                $notifyTitle = match ($type) {
                    'inventory_list' => 'Laporan Inventaris',
                    'stock_mutation' => 'Laporan Mutasi Stok',
                    'borrowing_history' => 'Laporan Peminjaman',
                    'low_stock' => 'Laporan Stok Menipis',
                    default => 'Laporan Sistem'
                };
                $request->user()->notify(new ReportReadyNotification($notifyTitle, $url));

                return response($pdfOutput)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="'.$filenameWithExt.'"')
                    ->header('Access-Control-Expose-Headers', 'Content-Disposition');
            }

            // Jika data besar, gunakan antrean (Queue)
            GenerateReportJob::dispatch($request->user(), $reportData, $startDate, $endDate, $location, $type);

            $this->logActivity('Laporan Diproses', "Meminta antrean laporan PDF tipe: {$type}");

            $message = 'Laporan sedang diproses karena ukuran data yang besar. Silakan cek menu Notifikasi dalam beberapa saat untuk mengunduh file.';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'type' => 'info',
                ]);
            }

            return back()->with('info', $message);
        }

        // Jalur Export Excel (Response Langsung menggunakan Builder Streaming)
        $excelService = new \App\Services\ExcelExportService;
        $this->logActivity('Laporan Diunduh (Excel)', "Mengunduh Excel tipe: {$type}");

        return match ($type) {
            'inventory_list' => $excelService->exportInventoryList($query, $filename),
            'stock_mutation' => $excelService->exportStockMutation($query, $filename),
            'borrowing_history' => $excelService->exportBorrowingHistory($query, $filename),
            'low_stock' => $excelService->exportLowStock($query, $filename),
            default => back()->with('error', 'Tipe laporan tidak didukung untuk export Excel.'),
        };
    }
}
