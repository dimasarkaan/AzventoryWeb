<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model (Blueprint Tabel Database) khusus untuk mencatat bukti pengembalian barang.
// Mendukung sistem pengembalian dicicil (parsial) atau dibayar lunas sekaligus (penuh).
class BorrowingReturn extends Model
{
    protected $table = 'borrowing_returns';

    protected $fillable = [
        'borrowing_id',
        'return_date',
        'quantity',
        'condition',
        'notes',
        'photos',
    ];

    protected $casts = [
        'return_date' => 'datetime',
        'photos' => 'array',
    ];

    // Relasi ke transaksi peminjaman induk.
    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class);
    }
}
