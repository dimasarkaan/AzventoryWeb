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

    /**
     * Mendapatkan kronologi kejadian untuk barang bermasalah (Rusak/Hilang)
     */
    public function getProblemChronologyAttribute()
    {
        if (!in_array($this->condition, ['Rusak', 'Hilang'])) {
            return null;
        }

        $conditionMap = ['Rusak' => 'bad', 'Hilang' => 'lost'];
        $returnCondition = $conditionMap[$this->condition] ?? null;

        if ($returnCondition) {
            // Cari riwayat return terakhir untuk part_number ini yang kondisinya bad/lost
            $latestReturn = \App\Models\BorrowingReturn::where('condition', $returnCondition)
                ->whereHas('borrowing.sparepart', function ($q) {
                    $q->where('part_number', $this->part_number);
                })
                ->latest()
                ->first();

            if ($latestReturn) {
                $userName = $latestReturn->borrowing->borrower_name ?? 'Seseorang';
                $date = $latestReturn->created_at->format('d M Y');
                $note = $latestReturn->notes ? " - Catatan: {$latestReturn->notes}" : "";
                return "Dikembalikan oleh {$userName} pada {$date}{$note}";
            }
        }

        // Fallback: check latest stock log
        $latestLog = $this->stockLogs()->latest()->first();
        if ($latestLog && $latestLog->reason) {
            $date = $latestLog->created_at->format('d M Y');
            return "Update log pada {$date} - {$latestLog->reason}";
        }

        return "Tidak ada riwayat catatan.";
    }
}
