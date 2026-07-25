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
     * Buat instance job baru dengan parameter pencarian.
     */
    public function __construct(User $user, array $params)
    {
        $this->user = $user;
        $this->params = $params;
    }

    /**
     * Eksekusi job.
     */
    public function handle(): void
    {
        // Rebuild query dynamically to avoid Payload Too Large exception in Queue
        $query = \App\Models\ActivityLog::with('user');
        $currentUser = $this->user;

        if ($currentUser->role === \App\Enums\UserRole::OPERATOR) {
            $query->where('user_id', $currentUser->id);
        } elseif ($currentUser->role === \App\Enums\UserRole::ADMIN) {
            $query->whereHas('user', function ($q) {
                $q->whereIn('role', [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::OPERATOR]);
            });
        }

        if (isset($this->params['role']) && $this->params['role'] && $this->params['role'] !== 'Semua Role') {
            $role = $this->params['role'];
            $query->whereHas('user', function ($q) use ($role) {
                $q->where('role', $role);
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
            $query->where(function ($q) use ($search) {
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
        Storage::disk('local')->put($path, $pdf->output());

        // Notify User
        $url = route('reports.file', ['filename' => $filename]);

        $this->user->notify(new ReportReadyNotification('Laporan Aktivitas Sistem', $url));
    }
}
