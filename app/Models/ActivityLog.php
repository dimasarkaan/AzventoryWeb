<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

// Model (Representasi Tabel Database) khusus untuk menyimpan Riwayat Aktivitas pengguna.
// Dilengkapi fitur "Prunable" yang akan otomatis membuang log usang agar database tidak cepat penuh.
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

    // Fitur Pembersih Otomatis (Pruning)
    // Akan otomatis menghapus catatan yang usianya sudah lebih dari 1 tahun (365 hari)
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
