<?php

namespace App\Http\Controllers\Inventory\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Mendapatkan daftar log aktivitas global (Hanya Superadmin).
     */
    public function index(Request $request)
    {
        // Hanya Superadmin yang boleh melihat log aktivitas global
        if ($request->user()->role !== \App\Enums\UserRole::SUPERADMIN) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya Superadmin yang diizinkan mengakses log aktivitas global',
            ], 403);
        }

        $query = ActivityLog::with('user');

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action')) {
            $query->where('action', 'like', '%'.$request->action.'%');
        }

        $logs = $query->latest()->paginate($request->input('per_page', 20));

        return response()->json([
            'status' => 'success',
            'data' => $logs,
        ]);
    }

    /**
     * Mendapatkan log aktivitas untuk pengguna tertentu (Bisa oleh Admin/Superadmin).
     */
    public function userLogs(Request $request, $userId)
    {
        $targetUser = \App\Models\User::findOrFail($userId);

        // Proteksi: Admin hanya boleh lihat log user lain (bukan Superadmin lain?)
        // Untuk sederhananya, kita batasi ke role Admin/Superadmin
        if (! in_array($request->user()->role, [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN])) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $logs = ActivityLog::where('user_id', $userId)
            ->with('user')
            ->latest()
            ->paginate($request->input('per_page', 20));

        return response()->json([
            'status' => 'success',
            'user' => $targetUser->name,
            'data' => $logs,
        ]);
    }
}
