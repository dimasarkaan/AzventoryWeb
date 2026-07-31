<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Model untuk tabel spareparts
class Sparepart extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'uuid', 'type', 'name', 'part_number', 'brand_id', 'category_id', 'location_id',
        'age', 'condition', 'color', 'price', 'minimum_stock',
        'stock', 'unit', 'status', 'image', 'qr_code_path',
    ];

    // Relasi ke tabel stock_logs
    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    // Relasi ke tabel borrowings
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    // Relasi ke tabel categories
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relasi ke tabel brands
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // Relasi ke tabel locations
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    // Mengecek apakah tipe barang adalah asset
    public function isAsset()
    {
        return $this->type === 'asset';
    }

    // Mengecek apakah tipe barang adalah sale
    public function isSaleable()
    {
        return $this->type === 'sale';
    }

    public function canBeBorrowed(int $quantity)
    {
        if ($this->type !== 'asset') {
            return 'Hanya barang bertipe Asset yang dapat dipinjam.';
        }

        if ($this->condition !== 'Baik') {
            return 'Hanya barang dengan kondisi "Baik" yang dapat dipinjam.';
        }

        if ($this->stock < $quantity) {
            return 'Stok tidak mencukupi untuk peminjaman ini.';
        }

        return true;
    }

    // Mendapatkan kronologi jika barang dilaporkan Rusak/Hilang
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

    // Scope: Mengambil barang dengan stok menipis atau habis
    public function scopeLowStock($query)
    {
        return $query->where('minimum_stock', '>', 0)
                     ->whereRaw('stock <= (minimum_stock + 5)')
                     ->where('condition', 'Baik');
    }

    // Scope: Mengambil barang dengan kondisi bermasalah (Rusak/Hilang)
    public function scopeProblematic($query)
    {
        return $query->whereIn('condition', ['Rusak', 'Hilang']);
    }

    // Scope: Mengambil barang jualan yang belum diatur harganya
    public function scopeNoPrice($query)
    {
        return $query->where('type', 'sale')
                     ->where(function ($q) {
                         $q->whereNull('price')->orWhere('price', '<=', 0);
                     });
    }

    // Mengecek apakah stok barang ini menipis
    public function isLowStock()
    {
        return $this->minimum_stock > 0 
            && $this->stock <= ($this->minimum_stock + 5) 
            && strtolower($this->condition) === 'baik';
    }

    // Mendapatkan status stok berdasarkan batas minimum secara konsisten dengan UI
    public function getStockStatusAttribute()
    {
        if ($this->stock <= 0) {
            return __('ui.status_out_of_stock');
        }

        if ($this->minimum_stock <= 0) {
            return 'Tanpa Batas';
        }

        if ($this->stock <= $this->minimum_stock) {
            return __('ui.status_critical');
        } elseif ($this->stock <= ($this->minimum_stock + 5)) {
            return __('ui.approaching_stock');
        }

        return __('ui.stock_safe');
    }

    // Menggunakan uuid sebagai route key
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Mendukung pencarian model melalui id atau uuid di route
    public function resolveRouteBinding($value, $field = null)
    {
        $field = $field ?? $this->getRouteKeyName();

        if ($field === 'uuid' && is_numeric($value)) {
            return $this->where('id', $value)->first();
        }

        return $this->where($field, $value)->first();
    }

    // Menentukan kolom yang otomatis diisi UUID
    public function uniqueIds()
    {
        return ['uuid'];
    }
}
