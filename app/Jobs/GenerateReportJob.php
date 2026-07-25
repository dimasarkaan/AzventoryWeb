<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\ReportReadyNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $reportData;

    protected $startDate;

    protected $endDate;

    protected $location;

    protected $type;

    /**
     * Buat instance job baru dengan parameter filter.
     */
    public function __construct(User $user, $startDate, $endDate, $location, $type)
    {
        $this->user = $user;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->location = $location;
        $this->type = $type;
    }

    /**
     * Eksekusi job.
     */
    public function handle(\App\Services\ReportService $reportService): void
    {
        Log::info('GenerateReportJob: Starting delayed PDF generation for user '.$this->user->id);

        // Fetch Data dynamically to avoid Payload Too Large exception in Queue
        $reportData = $reportService->getReportData($this->type, $this->location, $this->startDate, $this->endDate);
        
        $data = $reportData['data'];
        $title = $reportData['title'];
        $view = $reportData['view'];

        // Assign to local variables for the view
        $startDate = $this->startDate;
        $endDate = $this->endDate;
        $location = $this->location;
        $type = $this->type;

        // Generate PDF using a fresh resolved instance, bypassing Facade static caching in Queue Worker
        if ($view) {
            $pdf = app()->make('dompdf.wrapper')->loadView($view, compact('data', 'startDate', 'endDate', 'title', 'location', 'type'));

            if (in_array($type, ['borrowing_history', 'stock_mutation'])) {
                $pdf->setPaper('a4', 'landscape');
            } else {
                $pdf->setPaper('a4', 'portrait');
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
                $filename = "{$prefix}_{$start}sd{$end}.pdf";
            } elseif ($startDate) {
                $start = $startDate->format('d-m-Y');
                $filename = "{$prefix}_Sejak{$start}.pdf";
            } else {
                $filename = "{$prefix}SemuaRiwayat_".now()->format('d-m-Y').'.pdf';
            }

            // Save to Storage
            $path = 'reports/'.$filename;
            Storage::disk('local')->put($path, $pdf->output());

            // Notify User
            $url = route('reports.file', ['filename' => $filename]);

            // Use friendly title for notification
            $notifyTitle = match ($type) {
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
