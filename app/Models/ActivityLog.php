<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class ActivityLog extends Model
{
    use HasFactory, Prunable;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the prunable model query.
     */
    public function prunable()
    {
        // Hapus log yang lebih tua dari 1 tahun (365 hari)
        return static::where('created_at', '<=', now()->subDays(365));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
