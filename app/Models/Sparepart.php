<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'name',
        'part_number',
        'brand',
        'category',
        'location',
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
    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    public function isAsset()
    {
        return $this->type === 'asset';
    }

    public function isSaleable()
    {
        return $this->type === 'sale';
    }
}
