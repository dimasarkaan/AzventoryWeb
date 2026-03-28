<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Mengubah model Sparepart menjadi struktur JSON yang lebih rapi
 * agar digunakan oleh aplikasi klien / e-commerce dengan mudah.
 */
class SparepartResource extends JsonResource
{
    /**
     * Transform resource ke array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'part_number' => $this->part_number,
            'name' => $this->name,
            'brand' => $this->brand,
            'category' => $this->category,
            'type' => $this->type,
            'condition' => $this->condition,
            'status' => $this->status,
            'stock' => [
                'current' => (int) $this->stock,
                // Kirim null jika tidak dipantau — klien API bisa bedakan '0 minimum' vs 'tidak dipantau'
                'minimum' => $this->minimum_stock > 0 ? (int) $this->minimum_stock : null,
                'unit' => $this->unit,
                'is_low' => $this->minimum_stock > 0 && $this->stock <= $this->minimum_stock,
            ],
            'location' => $this->location,
            // Sembunyikan harga jika nilainya null, atau ubah menjadi float
            'price' => $this->when($this->price !== null, (float) $this->price),
            'image_url' => $this->image ? asset('storage/'.$this->image) : null,
            'qr_code_url' => $this->qr_code_path ? asset('storage/'.$this->qr_code_path) : null,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
        ];
    }
}
