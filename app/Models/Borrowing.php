<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Borrowing untuk mencatat transaksi peminjaman barang.
 */
class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'sparepart_id',
        'user_id',
        'borrower_name',
        'quantity',
        'borrowed_at',
        'expected_return_at',
        'returned_at',
        'notes',
        'status',
        'return_condition',
        'return_notes',
        'return_photos',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'expected_return_at' => 'datetime',
        'returned_at' => 'datetime',
        'return_photos' => 'array',
    ];

    // Relasi ke barang (sparepart) yang dipinjam.
    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }

    // Relasi ke user yang memproses peminjaman (bukan peminjam jika orang luar).
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke riwayat pengembalian (parsial).
    public function returns()
    {
        return $this->hasMany(BorrowingReturn::class);
    }

    /**
     * Aksesori untuk menghitung sisa barang yang belum dikembalikan.
     */
    public function getRemainingQuantityAttribute()
    {
        return $this->quantity - $this->returns()->sum('quantity');
    }

    /**
     * Memeriksa apakah peminjaman sudah melewati batas waktu (terlambat).
     */
    public function isOverdue()
    {
        if ($this->status === 'returned' || $this->remaining_quantity <= 0) {
            return false;
        }

        return $this->expected_return_at && $this->expected_return_at->startOfDay()->isPast();
    }
}
