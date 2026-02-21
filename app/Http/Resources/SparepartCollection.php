<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Mengubah koleksi data Inventaris menjadi struktur JSON standar, 
 * lengkap dengan informasi meta tambahan (bisa diperluas nantinya).
 */
class SparepartCollection extends ResourceCollection
{
    /**
     * Transform resource collection ke array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
            // Memberikan informasi tambahan versi API
            'meta' => [
                'api_version' => '1.0',
                'service' => 'Azventory Integration',
                // Opsional: informasi tambahan dari request
                'filters_applied' => $request->only(['search', 'category', 'brand', 'type']),
            ]
        ];
    }
}
