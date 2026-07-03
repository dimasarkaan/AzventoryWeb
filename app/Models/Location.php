<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_default', 'is_active'];

    /**
     * Relasi ke daftar sparepart yang disimpan di lokasi ini.
     */
    public function spareparts()
    {
        return $this->hasMany(Sparepart::class);
    }
}
