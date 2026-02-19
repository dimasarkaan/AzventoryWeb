<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Notifications\ReportReadyNotification;

class ExportActivityLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $params;

    // Buat instance job baru.
    public function __construct(User $user, array $params)
    {
        $this->user = $user;
        $this->params = $params;
    }

    // Eksekusi job.
    public function handle(): void
    {
        $query = ActivityLog::with('user');

        // Apply Filters (from Controller)
        if (isset($this->params['role']) && $this->params['role'] && $this->params['role'] !== 'Semua Role') {
            $query->whereHas('user', function ($q) {
                $q->where('role', $this->params['role']);
            });
        }
        if (isset($this->params['user_id']) && $this->params['user_id']) {
            $query->where('user_id', $this->params['user_id']);
        }
        if (isset($this->params['action']) && $this->params['action']) {
            $query->where('action', $this->params['action']);
        }
        if (isset($this->params['search']) && $this->params['search']) {
            $search = $this->params['search'];
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%");
            });
        }
        if (isset($this->params['start_date']) && $this->params['start_date']) {
            $query->whereDate('created_at', '>=', $this->params['start_date']);
        }
        if (isset($this->params['end_date']) && $this->params['end_date']) {
            $query->whereDate('created_at', '<=', $this->params['end_date']);
        }

        $logs = $query->latest()->get();

        // Generate PDF
        $pdf = Pdf::loadView('activity_logs.pdf', [
            'logs' => $logs,
            'isPdf' => true,
            // Pass params to view so header info is correct (Request parameters won't exist in Job)
            'request' => new \Illuminate\Http\Request($this->params) 
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
            $filename = 'LogAktivitasSemuaRiwayat_' . now()->format('d-m-Y') . '.pdf';
        }

        // Save to Storage
        $path = 'reports/' . $filename;
        Storage::disk('public')->put($path, $pdf->output());

        // Notify User
        $url = Storage::url($path);
        
        $this->user->notify(new ReportReadyNotification('Laporan Aktivitas Sistem', $url));
    }
}
