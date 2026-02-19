<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

/**
 * Model ActivityLog untuk mencatat riwayat aktivitas pengguna.
 * 
 * Menggunakan fitur Prunable untuk otomatis menghapus log lama.
 */
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
     * Query untuk pruning (pembersihan otomatis) model.
     * 
     * Menghapus log yang lebih tua dari 1 tahun.
     */
    public function prunable()
    {
        // Hapus log yang lebih tua dari 1 tahun (365 hari)
        return static::where('created_at', '<=', now()->subDays(365));
    }

    // Relasi ke user yang melakukan aktivitas.
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
