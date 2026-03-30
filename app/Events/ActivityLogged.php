<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class ActivityLogged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public $log;

    public $userName;

    public $userEmail;

    public $userRole;

    public function __construct($log, ?string $userName = null, ?string $userEmail = null, ?string $userRole = null)
    {
        $this->log = $log;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->userRole = $userRole;
    }

    /**
     * Dapatkan channel tempat event harus disiarkan.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('activity-logs'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->log->id,
            'user_name' => $this->userName ?? ($this->log->user ? $this->log->user->name : 'System'),
            'user_email' => $this->userEmail ?? ($this->log->user ? $this->log->user->email : '-'),
            'user_role' => $this->userRole ?? ($this->log->user ? ($this->log->user->role instanceof \App\Enums\UserRole ? $this->log->user->role->label() : ucfirst($this->log->user->role)) : '-'),
            'action' => $this->log->action,
            'description' => $this->log->description,
            'properties' => $this->log->properties,
            'created_at' => $this->log->created_at->toISOString(),
            'created_at_human' => $this->log->created_at->diffForHumans(),
        ];
    }

    /**
     * Nama event yang akan disiarkan ke frontend.
     */
    public function broadcastAs(): string
    {
        return 'ActivityLogged';
    }
}
