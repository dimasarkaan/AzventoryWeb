<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;

/**
 * ActivityLogController mengelola tampilan dan penyaringan riwayat aktivitas sistem.
 */
class ActivityLogController extends Controller
{
    use ActivityLogger;

    /**
     * Menampilkan daftar log aktivitas dengan fitur filter berdasarkan role, user, aksi, dan rentang tanggal.
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        $currentUser = $request->user();

        // Implementasi Hierarki Privasi:
        // Operator hanya melihat aktivitasnya sendiri.
        // Admin dapat melihat aktivitas Admin lain & Operator (bukan Superadmin).
        if ($currentUser->role === \App\Enums\UserRole::OPERATOR) {
            $query->where('user_id', $currentUser->id);
        } elseif ($currentUser->role === \App\Enums\UserRole::ADMIN) {
            $query->whereHas('user', function ($q) {
                $q->whereIn('role', [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::OPERATOR]);
            });
        }

        if ($request->has('role') && $request->role && $request->role !== 'Semua Role') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%");
            });
        }

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Pengelompokan tipe subjek secara virtual untuk mempermudah pencarian tematik
        if ($request->has('subject_type') && $request->subject_type && $request->subject_type !== 'Semua Tipe') {
            switch ($request->subject_type) {
                case 'inventory':
                    $query->where(function ($q) {
                        $q->where('action', 'like', '%inventory%')
                            ->orWhere('action', 'like', '%stock%')
                            ->orWhere('action', 'like', '%stok%')
                            ->orWhere('action', 'like', '%item%')
                            ->orWhere('action', 'like', '%barang%')
                            ->orWhere('action', 'like', '%sparepart%');
                    });
                    break;
                case 'user':
                    $query->where(function ($q) {
                        $q->where('action', 'like', '%user%')
                            ->orWhere('action', 'like', '%pengguna%')
                            ->orWhere('action', 'like', '%profile%')
                            ->orWhere('action', 'like', '%profil%')
                            ->orWhere('action', 'like', '%password%')
                            ->orWhere('action', 'like', '%role%');
                    });
                    break;
                case 'auth':
                    $query->where(function ($q) {
                        $q->where('action', 'like', '%login%')
                            ->orWhere('action', 'like', '%logout%');
                    });
                    break;
                case 'report':
                    $query->where(function ($q) {
                        $q->where('action', 'like', '%report%')
                            ->orWhere('action', 'like', '%export%')
                            ->orWhere('action', 'like', '%download%');
                    });
                    break;
            }
        }

        $activityLogs = $query->latest()->paginate(10);

        $usersQuery = \App\Models\User::orderBy('name');
        if ($currentUser->role === \App\Enums\UserRole::ADMIN) {
            $usersQuery->whereIn('role', [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::OPERATOR]);
        }
        $users = $usersQuery->get();

        $actionsQuery = ActivityLog::select('action')->distinct()->orderBy('action');
        if ($currentUser->role === \App\Enums\UserRole::ADMIN) {
            $actionsQuery->whereHas('user', function ($q) {
                $q->whereIn('role', [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::OPERATOR]);
            });
        }
        $actions = $actionsQuery->pluck('action');

        if ($request->wantsJson()) {
            return response()->json([
                'activityLogs' => $activityLogs,
                'last_id' => $activityLogs->first()?->id
            ]);
        }

        return view('reports.activity_logs.index', compact('activityLogs', 'users', 'actions'));
    }

    /**
     * Memproses permintaan export log ke dalam format PDF (melalui simulasi background) atau Excel.
     */
    public function export(Request $request)
    {
        $query = ActivityLog::with('user');

        $currentUser = $request->user();
        if ($currentUser->role === \App\Enums\UserRole::OPERATOR) {
            $query->where('user_id', $currentUser->id);
        } elseif ($currentUser->role === \App\Enums\UserRole::ADMIN) {
            $query->whereHas('user', function ($q) {
                $q->whereIn('role', [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::OPERATOR]);
            });
        }

        if ($request->has('role') && $request->role && $request->role !== 'Semua Role') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('action', 'like', "%{$search}%");
            });
        }

        if ($request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->latest()->get();
        $format = $request->input('format', 'pdf');

        if ($request->start_date && $request->end_date) {
            $start = \Carbon\Carbon::parse($request->start_date)->format('d-m-Y');
            $end = \Carbon\Carbon::parse($request->end_date)->format('d-m-Y');
            $filename = "LogAktivitas_{$start}sd{$end}";
        } elseif ($request->start_date) {
            $start = \Carbon\Carbon::parse($request->start_date)->format('d-m-Y');
            $filename = "LogAktivitas_Sejak{$start}";
        } else {
            $filename = 'LogAktivitasSemuaRiwayat_'.now()->format('d-m-Y');
        }

        if ($format === 'pdf') {
            // Jika data kecil (< 500 baris), langsung stream untuk kenyamanan pengguna cPanel.
            if ($logs->count() <= 500) {
                $this->logActivity('Export Log Aktivitas', 'Mengunduh log aktivitas (PDF Langsung).');
                
                $pdf = app()->make('dompdf.wrapper')->loadView('reports.activity_logs.pdf', [
                    'logs' => $logs,
                    'isPdf' => true,
                    'request' => $request,
                ]);

                return $pdf->download($filename . '.pdf');
            }

            \App\Jobs\ExportActivityLogJob::dispatch($request->user(), $request->all(), $logs);
            $this->logActivity('Export Log Aktivitas', 'Meminta antrean export log aktivitas (PDF).');

            return back()->with('success', 'Laporan PDF sedang diproses di latar belakang. Silakan cek menu Notifikasi dalam beberapa saat.');
        } else {
            $this->logActivity('Export Log Aktivitas', 'Mengunduh file log aktivitas (Excel).');

            $excelService = new \App\Services\ExcelExportService;

            return $excelService->exportActivityLogs($logs, $filename);
        }
    }
}
