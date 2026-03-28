<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Borrowing untuk mencatat setiap transaksi peminjaman barang oleh personil.
 */
class Borrowing extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'sparepart_id', 'user_id', 'borrower_name', 'quantity', 'borrowed_at',
        'expected_return_at', 'returned_at', 'notes', 'status',
        'return_condition', 'return_notes', 'return_photos',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'expected_return_at' => 'datetime',
        'returned_at' => 'datetime',
        'return_photos' => 'array',
    ];

    /**
     * Relasi ke aset atau sparepart yang sedang dipinjam.
     */
    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }

    /**
     * Relasi ke user (Staff/Admin) yang memvalidasi transaksi peminjaman ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke detail riwayat pengembalian barang (mendukung pengembalian bertahap).
     */
    public function returns()
    {
        return $this->hasMany(BorrowingReturn::class);
    }

    /**
     * Attribut virtual untuk menghitung sisa barang yang belum dikembalikan ke gudang.
     * Menggunakan returns_sum_quantity jika di-eager load untuk mencegah N+1 query.
     */
    public function getRemainingQuantityAttribute()
    {
        $returned = $this->attributes['returns_sum_quantity'] ?? $this->returns()->sum('quantity');

        return $this->quantity - $returned;
    }

    /**
     * Mengecek apakah peminjaman telah melewati batas waktu estimasi pengembalian.
     */
    public function isOverdue()
    {
        if ($this->status === 'returned' || $this->remaining_quantity <= 0) {
            return false;
        }

        return $this->expected_return_at && $this->expected_return_at->startOfDay()->isPast();
    }
}
