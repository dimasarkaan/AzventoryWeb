<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model Sparepart merepresentasikan barang inventory (Suku Cadang & Aset).
 */
class Sparepart extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'name',
        'part_number',
        'brand',
        'category',
        'location',
        'age',
        'condition',
        'color',
        'price', // nullable
        'stock',
        'minimum_stock',
        'unit',
        'status',
        'image',
        'qr_code_path',
    ];

    // Relasi ke log stok.
    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    // Relasi ke data peminjaman.
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    // Cek apakah item adalah aset.
    public function isAsset()
    {
        return $this->type === 'asset';
    }

    // Cek apakah item adalah barang jual.
    public function isSaleable()
    {
        return $this->type === 'sale';
    }

    /**
     * Mengecek apakah barang dapat dipinjam.
     * 
     * Validasi berdasarkan kondisi barang dan ketersediaan stok.
     * 
     * @param int $quantity Jumlah yang ingin dipinjam
     * @return bool|string True jika bisa, string error jika tidak
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
