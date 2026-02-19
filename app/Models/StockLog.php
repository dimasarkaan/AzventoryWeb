<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model StockLog untuk mencatat riwayat perubahan stok barang.
 */
class StockLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi ke user yang melakukan perubahan stok.
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke barang yang stoknya berubah.
    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
}
