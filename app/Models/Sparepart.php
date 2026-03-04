<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Sparepart merepresentasikan barang inventaris (Unit Sparepart, Aset, atau Barang Jual).
 */
class Sparepart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type', 'name', 'part_number', 'brand', 'category', 'location',
        'age', 'condition', 'color', 'price', 'minimum_stock',
        'stock', 'unit', 'status', 'image', 'qr_code_path',
    ];

    /**
     * Relasi ke riwayat perubahan stok (StockLog).
     */
    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    /**
     * Relasi ke data transaksi peminjaman.
     */
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    /**
     * Predikat untuk mengecek apakah barang masuk kategori aset perusahaan.
     */
    public function isAsset()
    {
        return $this->type === 'asset';
    }

    /**
     * Predikat untuk mengecek apakah barang tersebut diperjualbelikan.
     */
    public function isSaleable()
    {
        return $this->type === 'sale';
    }

    /**
     * Validasi kelayakan barang untuk dipinjam berdasarkan kondisi dan ketersediaan stok fisik.
     *
     * @return bool|string True jika layak, pesan error jika tidak memenuhi kriteria.
     */
    public function canBeBorrowed(int $quantity)
    {
        if ($this->condition !== 'Baik') {
            return 'Hanya barang dengan kondisi "Baik" yang dapat dipinjam.';
        }

        if ($this->stock < $quantity) {
            return 'Stok tidak mencukupi untuk peminjaman ini.';
        }

        return true;
    }
}
