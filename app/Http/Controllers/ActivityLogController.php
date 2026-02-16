<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Traits\ActivityLogger;

class ActivityLogController extends Controller
{
    use ActivityLogger;

    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filter by Role
        if ($request->has('role') && $request->role && $request->role !== 'Semua Role') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        // Filter by User
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by Action
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        // Search (Description or Action)
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%");
            });
        }

        // Filter by Date Range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by Subject Type (Derived)
        if ($request->has('subject_type') && $request->subject_type && $request->subject_type !== 'Semua Tipe') {
            switch ($request->subject_type) {
                case 'inventory':
                    $query->where(function($q) {
                        $q->where('action', 'like', '%inventory%')
                          ->orWhere('action', 'like', '%stock%')
                          ->orWhere('action', 'like', '%item%')
                          ->orWhere('action', 'like', '%barang%');
                    });
                    break;
                case 'user':
                    $query->where(function($q) {
                        $q->where('action', 'like', '%user%')
                          ->orWhere('action', 'like', '%profile%')
                          ->orWhere('action', 'like', '%password%')
                          ->orWhere('action', 'like', '%role%');
                    });
                    break;
                case 'auth':
                    $query->where(function($q) {
                        $q->where('action', 'like', '%login%')
                          ->orWhere('action', 'like', '%logout%');
                    });
                    break;
                 case 'report':
                    $query->where(function($q) {
                        $q->where('action', 'like', '%report%')
                          ->orWhere('action', 'like', '%export%')
                          ->orWhere('action', 'like', '%download%');
                    });
                    break;
            }
        }

        $activityLogs = $query->latest()->paginate(10);
        $users = \App\Models\User::orderBy('name')->get();
        // Get unique actions for filter
        $actions = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');

        return view('activity_logs.index', compact('activityLogs', 'users', 'actions'));
    }

    public function export(Request $request)
    {
        $query = ActivityLog::with('user');

        // Filter by Role
        if ($request->has('role') && $request->role && $request->role !== 'Semua Role') {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('role', $request->role);
            });
        }

        // Filter by User
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by Action
        if ($request->has('action') && $request->action) {
            $query->where('action', $request->action);
        }

        // Search (Description or Action)
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%");
            });
        }

        // Filter by Date Range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->latest()->get();
        $format = $request->input('format', 'pdf');

        if ($format === 'pdf') {
            \App\Jobs\ExportActivityLogJob::dispatch($request->user(), $request->all());
            
            $this->logActivity('Export Log Aktivitas', "Meminta export log aktivitas.");
            
            return back()->with('success', 'Export log sedang memproses. Anda akan menerima notifikasi saat file siap diunduh.');
        } else {
            $this->logActivity('Export Log Aktivitas', "Mengunduh log aktivitas format Excel.");
            
            // Generate Filename
            if ($request->start_date && $request->end_date) {
                // Format: LogAktivitas_01-01-2026sd15-01-2026
                $start = \Carbon\Carbon::parse($request->start_date)->format('d-m-Y');
                $end = \Carbon\Carbon::parse($request->end_date)->format('d-m-Y');
                $filename = "LogAktivitas_{$start}sd{$end}.xls";
            } elseif ($request->start_date) {
                $start = \Carbon\Carbon::parse($request->start_date)->format('d-m-Y');
                $filename = "LogAktivitas_Sejak{$start}.xls";
            } else {
                // Format: LogAktivitasSemuaRiwayat_30-01-2026
                $filename = 'LogAktivitasSemuaRiwayat_' . now()->format('d-m-Y') . '.xls';
            }
            
            return response(view('activity_logs.pdf', ['logs' => $logs, 'isPdf' => false]))
                ->header('Content-Type', 'application/vnd.ms-excel')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        }
    }
}
