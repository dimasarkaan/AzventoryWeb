<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Model (Blueprint Tabel Database) utama yang mewakili fisik Barang di gudang.
// Bisa berupa Aset Perusahaan atau Barang Jualan (Sale).
class Sparepart extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'uuid', 'type', 'name', 'part_number', 'brand_id', 'category_id', 'location_id',
        'age', 'condition', 'color', 'price', 'minimum_stock',
        'stock', 'unit', 'status', 'image', 'qr_code_path',
    ];

    // Relasi Database: Mengambil seluruh riwayat pergerakan (keluar/masuk/penyesuaian) stok untuk barang ini
    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    // Relasi Database: Mengambil semua riwayat peminjaman yang melibatkan barang ini
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    // Relasi Database: Menghubungkan barang ini dengan data Kategorinya
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi Database: Menghubungkan barang ini dengan data Mereknya
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // Relasi Database: Menghubungkan barang ini dengan lokasi rak penyimpanannya
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Fitur Pengecek (Helper): Mengecek apakah barang ini berstatus "Aset"
    public function isAsset()
    {
        return $this->type === 'asset';
    }

    // Fitur Pengecek (Helper): Mengecek apakah barang ini berstatus untuk dijual ("Sale")
    public function isSaleable()
    {
        return $this->type === 'sale';
    }

    // Sistem Validasi Cerdas: Mencegah barang yang rusak atau stoknya habis agar tidak bisa dipinjam
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

    // Fitur Penyelidik (Detektif): Mencari tahu riwayat atau kronologi kejadian jika barang dilaporkan Rusak/Hilang
    public function getProblemChronologyAttribute()
    {
        if (! in_array($this->condition, ['Rusak', 'Hilang'])) {
            return null;
        }

        $conditionMap = ['Rusak' => 'bad', 'Hilang' => 'lost'];
        $returnCondition = $conditionMap[$this->condition] ?? null;

        // Lapis 1: Cek dari Riwayat Pengembalian Peminjaman (Peminjam)
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
                $note = $latestReturn->notes ? " - Catatan: {$latestReturn->notes}" : '';

                return "Dikembalikan ({$this->condition}) oleh {$userName} pada {$date}{$note}";
            }
        }

        // Lapis 2: Cek dari Log Aktivitas (Admin Edit Manual via Dashboard)
        $latestActivity = \App\Models\ActivityLog::where('action', 'Sparepart Diperbarui')
            ->where('description', 'like', '%' . $this->part_number . '%')
            ->where('properties->condition->new', $this->condition)
            ->latest()
            ->first();

        if ($latestActivity) {
            $userName = $latestActivity->user->name ?? 'Admin';
            $date = $latestActivity->created_at->format('d M Y');
            return "Status diubah manual menjadi {$this->condition} oleh {$userName} pada {$date}";
        }

        // Lapis 3: Fallback check latest stock log (Penyesuaian Fisik Gudang)
        $latestLog = $this->stockLogs->first();
        if ($latestLog && $latestLog->reason) {
            $date = $latestLog->created_at->format('d M Y');

            return "Update stok pada {$date} - {$latestLog->reason}";
        }

        return 'Tidak ada riwayat catatan spesifik.';
    }

    // Scope (Standarisasi Global): Mengambil data barang yang stoknya sudah Menuju Kritis (<= 150%) atau Habis
    public function scopeLowStock($query)
    {
        return $query->where('minimum_stock', '>', 0)
                     ->whereRaw('stock <= (minimum_stock + 5)')
                     ->where('condition', 'Baik');
    }

    // Scope (Standarisasi Global): Mengambil data barang yang bermasalah (kondisi Rusak atau Hilang)
    public function scopeProblematic($query)
    {
        return $query->whereIn('condition', ['Rusak', 'Hilang']);
    }

    // Scope (Standarisasi Global): Mengambil data barang jualan (Sale) yang belum diatur harganya
    public function scopeNoPrice($query)
    {
        return $query->where('type', 'sale')
                     ->where(function ($q) {
                         $q->whereNull('price')->orWhere('price', '<=', 0);
                     });
    }

    // Helper (Standarisasi Global): Mengecek apakah SATU barang spesifik ini sedang menipis
    public function isLowStock()
    {
        return $this->minimum_stock > 0 
            && $this->stock <= ($this->minimum_stock + 5) 
            && strtolower($this->condition) === 'baik';
    }

    // Keamanan URL: Menggunakan huruf acak (UUID) di URL Profil barang agar ID aslinya tidak mudah ditebak peretas
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Kompatibilitas URL: Memastikan tautan/link lama yang masih menggunakan angka ID biasa tetap berfungsi normal
    public function resolveRouteBinding($value, $field = null)
    {
        $field = $field ?? $this->getRouteKeyName();

        if ($field === 'uuid' && is_numeric($value)) {
            return $this->where('id', $value)->first();
        }

        return $this->where($field, $value)->first();
    }

    // Mendaftarkan kolom 'uuid' agar otomatis diisi gabungan angka/huruf acak oleh sistem saat barang baru masuk
    public function uniqueIds()
    {
        return ['uuid'];
    }
}
