<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        // View for report selection if needed, or just specific methods
        return view('superadmin.reports.index');
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

            return back()->with('success', 'Laporan sedang memproses. Anda akan menerima notifikasi saat laporan siap diunduh.');
        }

        // If Excel, process synchronously (usually faster/lighter)
        // Note: For a perfect architecture, we should extract the data fetching logic into a Service 
        // to avoid duplication between this Controller and GenerateReportJob.
        // For now, we keep the Excel logic here for immediate download support.
        
        $startDate = null;
        $endDate = null;
        $location = $request->input('location');
        if (empty($location)) {
            $location = 'all';
        }

        // Date Logic
        if ($period == 'custom') {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
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

        $data = collect();
        $title = 'Laporan';
        $view = '';

        if ($type == 'inventory_list') {
            $query = Sparepart::orderBy('name');
            if ($location !== 'all') {
                $query->where('location', $location);
            }
            $data = $query->get();
            $title = 'Laporan Data Inventaris Saat Ini';
            $view = 'superadmin.reports.pdf_inventory_list'; // Reusing view for Excel table structure

        } elseif ($type == 'stock_mutation') {
            $query = StockLog::with(['sparepart', 'user']);
            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
            if ($location !== 'all') {
                $query->whereHas('sparepart', function($q) use ($location) {
                    $q->where('location', $location);
                });
            }
            $data = $query->latest()->get();
            $title = 'Laporan Riwayat Stok / Mutasi';
            $view = 'superadmin.reports.pdf_stock_mutation';

        } elseif ($type == 'borrowing_history') {
            $query = \App\Models\Borrowing::with(['sparepart', 'user']); 
            if ($startDate && $endDate) {
                $query->whereBetween('borrowed_at', [$startDate, $endDate]);
            }
            $data = $query->latest()->get();
            $title = 'Laporan Riwayat Peminjaman';
            $view = 'superadmin.reports.pdf_borrowing_history';

        } elseif ($type == 'low_stock') {
            $query = Sparepart::whereColumn('stock', '<=', 'minimum_stock')->orderBy('stock', 'asc');
             if ($location !== 'all') {
                $query->where('location', $location);
            }
            $data = $query->get();
            $title = 'Laporan Stok Menipis';
            $view = 'superadmin.reports.pdf_low_stock';
        }

        // Return view with Excel headers for "Formatted Excel"
        $filename = 'laporan_' . $type . '_' . now()->format('YmdHis') . '.xls';
        
        return response(view($view, compact('data', 'startDate', 'endDate', 'title', 'location', 'type')))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
