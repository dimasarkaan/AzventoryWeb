<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

use App\Events\ActivityLogged;

trait ActivityLogger
{
    // Catat aktivitas sistem ke database + broadcast.
    protected function logActivity(string $action, string $description, array $properties = null): void
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
