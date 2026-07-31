<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Model (Blueprint Tabel Database) yang berfungsi seperti Buku Kas Stok (Buku Mutasi).
// Merupakan CCTV untuk barang: Mencatat siapa yang nambah, ngurangin, dan siapa yang menyetujui.
class StockLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sparepart_id', 'user_id', 'type', 'quantity', 'reason', 'status', 'approved_by', 'rejection_reason',
    ];

    // Relasi Database: Mencari tahu siapa pemohon/operator yang mengubah stok ini
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // Relasi Database: Mencari tahu barang/aset apa yang stoknya berubah
    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class)->withTrashed();
    }

    // Relasi Database: Mencari tahu siapa atasan yang me-ACC (menyetujui) permintaan ini
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by')->withTrashed();
    }
}
