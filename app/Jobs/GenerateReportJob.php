<?php

namespace App\Jobs;

use App\Models\Sparepart;
use App\Models\StockLog;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Notifications\ReportReadyNotification;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $params;

    /**
     * Create a new job instance.
     * 
     * @param User $user The user requesting the report
     * @param array $params Filter parameters (type, period, location, etc.)
     */
    public function __construct(User $user, array $params)
    {
        $this->user = $user;
        $this->params = $params;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('GenerateReportJob: Starting job for user ' . $this->user->id . ' with params: ' . json_encode($this->params)); // Added Log call

        // Extract parameters
        $type = $this->params['type'] ?? $this->params['report_type'] ?? 'inventory_list'; // Fallback to inventory_list or handle default
        $period = $this->params['period'] ?? 'all';
        // $format = $this->params['format'] ?? 'pdf'; // Assumed PDF for now as CSV is fast enough synchronously usually, but let's handle PDF.
        
        $location = $this->params['location'] ?? 'all';
        $startDate = null;
        $endDate = null;

        // Date Logic (Replicated from Controller)
        if ($period == 'custom' && isset($this->params['start_date'], $this->params['end_date'])) {
            $startDate = Carbon::parse($this->params['start_date'])->startOfDay();
            $endDate = Carbon::parse($this->params['end_date'])->endOfDay();
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

        // Query Logic (Replicated from Controller)
        if ($type == 'inventory_list') {
            $query = Sparepart::orderBy('name');
            if ($location !== 'all') {
                $query->where('location', $location);
            }
            $data = $query->get();
            $title = 'Laporan Data Inventaris Saat Ini';
            $view = 'superadmin.reports.pdf_inventory_list';

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

        // Generate PDF
        if ($view) {
            $pdf = Pdf::loadView($view, compact('data', 'startDate', 'endDate', 'title', 'location', 'type'));
            
            if (in_array($type, ['borrowing_history', 'stock_mutation'])) {
                $pdf->setPaper('a4', 'landscape');
            }

            // Save to Storage
            $filename = 'reports/laporan_' . $type . '_' . now()->format('YmdHis') . '_' . \Illuminate\Support\Str::random(10) . '.pdf';
            Storage::disk('public')->put($filename, $pdf->output());
            \Illuminate\Support\Facades\Log::info('GenerateReportJob: File saved to ' . $filename);

            // Notify User
            $url = Storage::disk('public')->url($filename); 
            
            // Send Notification
            $this->user->notify(new ReportReadyNotification($title, $url));
            \Illuminate\Support\Facades\Log::info('GenerateReportJob: Notification sent to user ' . $this->user->id);
        }
    }
}
