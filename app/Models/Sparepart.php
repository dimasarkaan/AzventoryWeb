<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'part_number',
        'category',
        'location',
        'condition',
        'price',
        'stock',
        'status',
    ];
}
