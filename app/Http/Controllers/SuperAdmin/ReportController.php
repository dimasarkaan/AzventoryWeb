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
        $csvHeader = [];
        $csvCallback = null; // Callback to map row to CSV array

        if ($type == 'inventory_list') {
            // Snapshot of current inventory
            $query = Sparepart::orderBy('name');
            
            if ($location !== 'all') {
                $query->where('location', $location);
            }

            $data = $query->get();
            $title = 'Laporan Data Inventaris Saat Ini';
            $view = 'superadmin.reports.pdf_inventory_list';
            
            $csvHeader = ['Kode Part', 'Nama Barang', 'Kategori', 'Lokasi', 'Stok', 'Satuan'];
            $csvCallback = function($row) {
                return [$row->part_number, $row->name, $row->category, $row->location, $row->stock, $row->unit];
            };

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
            
            $csvHeader = ['Tanggal', 'User', 'Barang', 'Tipe', 'Jumlah', 'Status', 'Catatan'];
            $csvCallback = function($row) {
                return [
                    $row->created_at->format('Y-m-d H:i'),
                    $row->user->name ?? 'System',
                    $row->sparepart->name ?? 'Unknown',
                    strtoupper($row->type),
                    $row->quantity,
                    ucfirst($row->status),
                    $row->reason
                ];
            };

        } elseif ($type == 'borrowing_history') {
            // Borrowing History
            $query = \App\Models\Borrowing::with(['sparepart', 'user']); // Ensure model import

            if ($startDate && $endDate) {
                $query->whereBetween('borrowed_at', [$startDate, $endDate]);
            }

            // Optional: Filter by status if needed, currently showing all history
            
            $data = $query->latest()->get();
            $title = 'Laporan Riwayat Peminjaman';
            $view = 'superadmin.reports.pdf_borrowing_history';

            $csvHeader = ['Peminjam', 'Barang', 'Jumlah', 'Tgl Pinjam', 'Tgl Kembali (Rencana)', 'Status', 'Kondisi Kembali'];
            $csvCallback = function($row) {
                return [
                    $row->user->name ?? $row->borrower_name,
                    $row->sparepart->name ?? 'Unknown',
                    $row->quantity,
                    $row->borrowed_at->format('Y-m-d'),
                    $row->expected_return_at ? $row->expected_return_at->format('Y-m-d') : '-',
                    ucfirst($row->status),
                    $row->return_condition ?? '-'
                ];
            };

        } elseif ($type == 'low_stock') {
            // Low Stock Items
            $query = Sparepart::whereColumn('stock', '<=', 'minimum_stock')->orderBy('stock', 'asc');
             if ($location !== 'all') {
                $query->where('location', $location);
            }
            
            $data = $query->get();
            $title = 'Laporan Stok Menipis';
            $view = 'superadmin.reports.pdf_low_stock';

             $csvHeader = ['Kode Part', 'Nama Barang', 'Lokasi', 'Sisa Stok', 'Min. Stok', 'Status'];
            $csvCallback = function($row) {
                return [
                    $row->part_number, 
                    $row->name, 
                    $row->location, 
                    $row->stock, 
                    $row->minimum_stock, 
                    'CRITICAL'
                ];
            };
        }

        // Handle Export Format
        if ($format == 'excel') { 
            // Return view with Excel headers for "Formatted Excel"
            $filename = 'laporan_' . $type . '_' . now()->format('YmdHis') . '.xls';
            
            return response(view($view, compact('data', 'startDate', 'endDate', 'title', 'location', 'type')))
                ->header('Content-Type', 'application/vnd.ms-excel')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }

        // Default: PDF Export
        $pdf = Pdf::loadView($view, compact('data', 'startDate', 'endDate', 'title', 'location', 'type'));
        // Landscape for some reports if needed
        if (in_array($type, ['borrowing_history', 'stock_mutation'])) {
            $pdf->setPaper('a4', 'landscape');
        }
        return $pdf->download('laporan_azventory_' . now()->format('YmdHis') . '.pdf');
    }
}
