<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model StockLog mencatat audit trail setiap perubahan stok (Masuk/Keluar/Penyesuaian).
 */
class StockLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'sparepart_id', 'user_id', 'type', 'quantity', 'reason', 'status', 'approved_by', 'rejection_reason',
    ];

    /**
     * Relasi ke user yang melakukan atau mengajukan perubahan stok.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke data sparepart/aset yang mengalami perubahan stok.
     */
    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }

    /**
     * Relasi ke user yang menyetujui pengajuan ini.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
