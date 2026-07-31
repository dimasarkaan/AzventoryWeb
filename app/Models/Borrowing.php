<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Model (Blueprint Tabel Database) khusus untuk mencatat transaksi peminjaman barang.
// Menyimpan jejak barang apa yang dipinjam, dipinjam oleh siapa, dan status pengembaliannya.
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

    // Relasi Database: Menghubungkan catatan transaksi ini dengan fisik barang di gudang
    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class)->withTrashed();
    }

    // Relasi Database: Menghubungkan catatan ini dengan akun pengguna yang bertanggung jawab
    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    // Relasi Database: Menarik semua riwayat pengembalian (termasuk yang dicicil sedikit-sedikit)
    public function returns()
    {
        return $this->hasMany(BorrowingReturn::class);
    }

    // Atribut Virtual: Kolom jadi-jadian (tidak ada di database) untuk menghitung sisa barang pinjaman.
    // Menghitung otomatis: Total Pinjam dikurangi Total yang sudah Dikembalikan.
    public function getRemainingQuantityAttribute()
    {
        $returned = $this->attributes['returns_sum_quantity'] ?? $this->returns()->sum('quantity');

        return $this->quantity - $returned;
    }

    // Fitur Pengecek Status: Memastikan apakah si peminjam sudah telat mengembalikan barang (Lewat Jatuh Tempo)
    public function isOverdue()
    {
        if ($this->status === 'returned' || $this->remaining_quantity <= 0) {
            return false;
        }

        return $this->expected_return_at && $this->expected_return_at->endOfDay()->isPast();
    }

    // Scope (Standarisasi Global): Mengambil data peminjaman yang masih aktif (belum dikembalikan penuh)
    public function scopeActive($query)
    {
        return $query->where('status', 'borrowed');
    }

    // Scope (Standarisasi Global): Mengambil data peminjaman yang aktif dan sudah melewati batas waktu (Overdue)
    public function scopeOverdue($query)
    {
        return $query->active()->where('expected_return_at', '<', now()->startOfDay());
    }
}
