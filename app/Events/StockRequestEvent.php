<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockRequestEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Buat instance event baru.
     */
    public function __construct(
        public User $user,
        public string $message,
        public string $url,
        public int $unread_count
    ) {
        //
    }

    /**
     * Dapatkan channel tempat event harus disiarkan.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.'.$this->user->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'stock.request';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message,
            'url' => $this->url,
            'unread_count' => $this->unread_count,
        ];
    }
}
