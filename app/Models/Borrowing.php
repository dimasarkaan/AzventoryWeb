<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'sparepart_id',
        'user_id',
        'borrower_name',
        'quantity',
        'borrowed_at',
        'expected_return_at',
        'returned_at',
        'notes',
        'status',
        'return_condition',
        'return_notes',
        'return_photos',
    ];

    protected $casts = [
        'borrowed_at' => 'datetime',
        'expected_return_at' => 'datetime',
        'returned_at' => 'datetime',
        'return_photos' => 'array',
    ];

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
