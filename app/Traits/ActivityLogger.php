<?php

namespace App\Traits;

use App\Events\ActivityLogged;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

/**
 * Trait ActivityLogger menyediakan fungsi global untuk mencatat log aktivitas sistem.
 */
trait ActivityLogger
{
    /**
     * Mencatat aktivitas ke database dan memancarkan (broadcast) event secara real-time.
     */
    protected function logActivity(string $action, string $description, ?array $properties = null): void
    {
        // Metadata dasar untuk audit keamanan
        $metadata = [
            'ip' => request()->header('X-Forwarded-For')
                    ? explode(',', request()->header('X-Forwarded-For'))[0]
                    : request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ];

        $log = ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'properties' => array_merge($metadata, $properties ?? []),
        ]);

        try {
            // Ambil info user saat ini untuk broadcast agar tidak 'System'
            $user = Auth::user();
            $userName = $user ? $user->name : 'System';
            $userEmail = $user ? $user->email : '-';
            $userRole = $user ? ($user->role instanceof \App\Enums\UserRole ? $user->role->label() : ucfirst($user->role)) : '-';

            broadcast(new ActivityLogged($log, $userName, $userEmail, $userRole));

            // Dashboard Sync: Update global 'last_updated' timestamp to trigger cache refresh
            \Illuminate\Support\Facades\Cache::forever('inventory_last_updated', now()->timestamp);
        } catch (\Throwable $e) {
            // Log error agar bisa didebug lewat storage/logs/laravel.log
            \Illuminate\Support\Facades\Log::error('Broadcasting ActivityLogged failed: '.$e->getMessage());
        }
    }
}
