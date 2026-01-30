<?php

namespace App\Services;

use App\Models\Sparepart;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class InventoryService
{
    /**
     * Get filtered spareparts with pagination.
     */
    public function getFilteredSpareparts(array $filters, int $perPage = 10)
    {
        $query = Sparepart::query();

        // Search Scope
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('part_number', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('brand', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Filters
        $this->applyExactFilter($query, 'brand', $filters['brand'] ?? null, 'Semua Merk');
        $this->applyExactFilter($query, 'category', $filters['category'] ?? null, 'Semua Kategori');
        $this->applyExactFilter($query, 'location', $filters['location'] ?? null, 'Semua Lokasi');
        $this->applyExactFilter($query, 'color', $filters['color'] ?? null, 'Semua Warna');

        // Special Filter
        if (($filters['filter'] ?? '') === 'low_stock') {
            $query->whereColumn('stock', '<=', 'minimum_stock');
        }

        // Sorting
        $this->applySorting($query, $filters['sort'] ?? null);

        return $query->paginate($perPage)->appends($filters);
    }

    /**
     * Get unique values for dropdowns (Cached).
     */
    public function getDropdownOptions()
    {
        return [
            'categories' => Cache::remember('inventory_categories', 3600, fn() => Sparepart::select('category')->distinct()->pluck('category')),
            'brands' => Cache::remember('inventory_brands', 3600, fn() => Sparepart::whereNotNull('brand')->select('brand')->distinct()->pluck('brand')),
            'locations' => Cache::remember('inventory_locations', 3600, fn() => Sparepart::select('location')->distinct()->pluck('location')),
            'colors' => Cache::remember('inventory_colors', 3600, fn() => Sparepart::whereNotNull('color')->select('color')->distinct()->pluck('color')),
        ];
    }

    /**
     * Clear inventory caches.
     */
    public function clearCache()
    {
        Cache::forget('inventory_categories');
        Cache::forget('inventory_brands');
        Cache::forget('inventory_locations');
        Cache::forget('inventory_colors');
    }

    private function applyExactFilter(Builder $query, string $column, ?string $value, string $ignoreValue)
    {
        if ($value && $value !== $ignoreValue) {
            $query->where($column, $value);
        }
    }

    private function applySorting(Builder $query, ?string $sort)
    {
        if (!$sort) {
            $query->latest();
            return;
        }

        switch ($sort) {
            case 'name_asc': $query->orderBy('name', 'asc'); break;
            case 'name_desc': $query->orderBy('name', 'desc'); break;
            case 'stock_asc': $query->orderBy('stock', 'asc'); break;
            case 'stock_desc': $query->orderBy('stock', 'desc'); break;
            case 'price_asc': $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'oldest': $query->oldest(); break;
            case 'newest': default: $query->latest(); break;
        }
    }
}
