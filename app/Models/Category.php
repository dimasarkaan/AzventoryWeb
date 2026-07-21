<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_default', 'is_active'];

    // Relasi Database: Mengambil semua daftar barang (Sparepart) yang tergabung di kategori ini
    public function spareparts()
    {
        return $this->hasMany(Sparepart::class);
    }
}
