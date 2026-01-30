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
use App\Services\ReportService;

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
    public function handle(ReportService $reportService): void
    {
        Log::info('GenerateReportJob: Starting job for user ' . $this->user->id . ' with params: ' . json_encode($this->params)); // Added Log call

        // Extract parameters
        $type = $this->params['type'] ?? $this->params['report_type'] ?? 'inventory_list'; // Fallback to inventory_list or handle default
        $location = $this->params['location'] ?? 'all';
        $startDateParam = $this->params['start_date'] ?? null;
        $endDateParam = $this->params['end_date'] ?? null;
        $period = $this->params['period'] ?? 'all';

        // Resolve Dates
        [$startDate, $endDate] = $reportService->resolveDateRange($period, $startDateParam, $endDateParam);

        // Fetch Data via Service
        $reportData = $reportService->getReportData($type, $location, $startDate, $endDate);
        
        $data = $reportData['data'];
        $title = $reportData['title'];
        $view = $reportData['view'];

        // Generate PDF
        if ($view) {
            $pdf = Pdf::loadView($view, compact('data', 'startDate', 'endDate', 'title', 'location', 'type'));
            
            if (in_array($type, ['borrowing_history', 'stock_mutation'])) {
                $pdf->setPaper('a4', 'landscape');
            }

            // Generate Filename
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
                $filename = "{$prefix}_{$start}sd{$end}.pdf";
            } else {
                $filename = "{$prefix}SemuaRiwayat_" . now()->format('d-m-Y') . ".pdf";
            }

            // Save to Storage
            $path = 'reports/' . $filename;
            Storage::disk('public')->put($path, $pdf->output());

            // Notify User
            $url = Storage::url($path);
            
            // Use friendly title for notification
            $notifyTitle = match($type) {
                'inventory_list' => 'Laporan Inventaris',
                'stock_mutation' => 'Laporan Mutasi Stok',
                'borrowing_history' => 'Laporan Peminjaman',
                'low_stock' => 'Laporan Stok Menipis',
                default => 'Laporan Sistem'
            };
            
            $this->user->notify(new ReportReadyNotification($notifyTitle, $url));
        }
    }
}
