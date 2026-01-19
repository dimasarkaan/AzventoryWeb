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
        $type = $request->input('report_type', 'inventory'); // inventory (list), mutation (logs)
        $period = $request->input('period', 'all');
        $startDate = null;
        $endDate = null;

        $location = $request->input('location', 'all');

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

        if ($type == 'inventory_list') {
            // Snapshot of current inventory
            $query = Sparepart::orderBy('name');
            
            if ($location !== 'all') {
                $query->where('location', $location);
            }

            $data = $query->get();
            $title = 'Laporan Data Inventaris Saat Ini';
            $view = 'superadmin.reports.pdf_inventory_list';

        } elseif ($type == 'stock_mutation') {
            // Logs
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
        }

        $pdf = Pdf::loadView($view, compact('data', 'startDate', 'endDate', 'title', 'location'));
        return $pdf->download('laporan_azventory_' . now()->format('YmdHis') . '.pdf');
    }
}
