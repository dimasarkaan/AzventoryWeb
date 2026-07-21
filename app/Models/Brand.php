<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_active'];

    // Relasi Database: Mengambil semua daftar barang (Sparepart) yang menggunakan merek ini
    public function spareparts()
    {
        return $this->hasMany(Sparepart::class);
    }
}
