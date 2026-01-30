<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Traits\ActivityLogger;
use App\Services\ReportService; // Added this line

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

    public function index()
    {
        // Get locations from InventoryService
        $options = $this->inventoryService->getDropdownOptions();
        $locations = $options['locations'];

        return view('superadmin.reports.index', compact('locations'));
    }

    public function download(Request $request)
    {
        $type = $request->input('report_type', 'inventory'); // inventory, mutation, borrowing, low_stock
        $period = $request->input('period', 'all');
        $format = $request->input('export_format', 'pdf'); // pdf, excel (csv)
        
        $params = $request->all();

        // If PDF, process in background to prevent timeout
        if ($format !== 'excel') {
            \App\Jobs\GenerateReportJob::dispatch($request->user(), $params);

            $this->logActivity('Laporan Diproses', "Meminta laporan PDF tipe: {$type}");

            return back()->with('success', 'Laporan sedang memproses. Anda akan menerima notifikasi saat laporan siap diunduh.');
        }

        // If Excel, use Service to fetch data
        
        $location = $request->input('location', 'all');
        
        // Resolve Dates
        [$startDate, $endDate] = $this->reportService->resolveDateRange(
            $period, 
            $request->input('start_date'), 
            $request->input('end_date')
        );

        // Fetch Data via Service
        $reportData = $this->reportService->getReportData($type, $location, $startDate, $endDate);
        
        $data = $reportData['data'];
        $title = $reportData['title'];
        $view = $reportData['view'];

        // Generate Filename (Standardized)
         $prefix = match($type) {
            'inventory_list' => 'LaporanInventaris',
            'stock_mutation' => 'LaporanMutasi',
            'borrowing_history' => 'LaporanPeminjaman',
            'low_stock' => 'LaporanStokMenipis',
            default => 'Laporan'
        };

        if ($startDate && $endDate) {
            $start = $startDate->format('d-m-Y');
            $end = $endDate->format('d-m-Y');
            $filename = "{$prefix}_{$start}sd{$end}.xls";
        } else {
             $filename = "{$prefix}SemuaRiwayat_" . now()->format('d-m-Y') . ".xls";
        }
        
        $this->logActivity('Laporan Diunduh', "Mengunduh laporan Excel tipe: {$type}");

        // Return Excel View
        return response(view($view, compact('data', 'startDate', 'endDate', 'title', 'location', 'type')))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
