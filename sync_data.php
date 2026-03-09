<?php
use App\Models\Sparepart;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Location;
use Illuminate\Support\Facades\Cache;

$categories = Sparepart::select('category')->distinct()->pluck('category')->filter();
foreach($categories as $cat) {
    Category::firstOrCreate(['name' => $cat]);
}
echo "Synced " . $categories->count() . " categories.\n";

$brands = Sparepart::select('brand')->distinct()->pluck('brand')->filter();
foreach($brands as $brand) {
    Brand::firstOrCreate(['name' => $brand]);
}
echo "Synced " . $brands->count() . " brands.\n";

$locations = Sparepart::select('location')->distinct()->pluck('location')->filter();
foreach($locations as $loc) {
    Location::firstOrCreate(['name' => $loc]);
}
echo "Synced " . $locations->count() . " locations.\n";

app(\App\Services\InventoryService::class)->clearCache();
echo "Cache cleared!\n";
