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
        $log = ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'properties' => $properties,
        ]);

        broadcast(new ActivityLogged($log));
    }
}
