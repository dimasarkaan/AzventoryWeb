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
use Illuminate\Support\Facades\Storage;

class ExportActivityLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    protected $params;

    protected $logs;

    /**
     * Buat instance job baru dengan snapshot data real-time.
     */
    public function __construct(User $user, array $params, $logs)
    {
        $this->user = $user;
        $this->params = $params;
        $this->logs = $logs;
    }

    /**
     * Eksekusi job.
     */
    public function handle(): void
    {
        // Fetch snapshot from constructor memory, immune to delayed data drift
        $logs = $this->logs;

        // Eager load relationships that might have been lost during job serialization
        $logs->loadMissing('user');

        // Generate PDF using a fresh resolved instance, bypassing Facade static caching in Queue Worker
        $pdf = app()->make('dompdf.wrapper')->loadView('reports.activity_logs.pdf', [
            'logs' => $logs,
            'isPdf' => true,
            // Pass params to view so header info is correct (Request parameters won't exist in Job)
            'request' => new \Illuminate\Http\Request($this->params),
        ]);

        // Generate Filename
        if (isset($this->params['start_date']) && isset($this->params['end_date']) && $this->params['start_date'] && $this->params['end_date']) {
            $start = \Carbon\Carbon::parse($this->params['start_date'])->format('d-m-Y');
            $end = \Carbon\Carbon::parse($this->params['end_date'])->format('d-m-Y');
            $filename = "LogAktivitas_{$start}sd{$end}.pdf";
        } elseif (isset($this->params['start_date']) && $this->params['start_date']) {
            $start = \Carbon\Carbon::parse($this->params['start_date'])->format('d-m-Y');
            $filename = "LogAktivitas_Sejak{$start}.pdf";
        } else {
            $filename = 'LogAktivitasSemuaRiwayat_'.now()->format('d-m-Y').'.pdf';
        }

        // Save to Storage
        $path = 'reports/'.$filename;
        Storage::disk('public')->put($path, $pdf->output());

        // Notify User
        $url = Storage::url($path);

        $this->user->notify(new ReportReadyNotification('Laporan Aktivitas Sistem', $url));
    }
}
