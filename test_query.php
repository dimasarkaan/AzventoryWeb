<?php require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(App\Services\InventoryService::class);
$query = App\Models\Sparepart::with(['brand', 'category', 'location']);

// Dump the query before
echo "Before:\n";
echo $query->toSql() . "\n";

$service->getFilteredSpareparts(['condition' => 'Rusak']);

echo "\nAfter getFilteredSpareparts(['condition' => 'Rusak']):\n";
// Wait, getFilteredSpareparts returns a LengthAwarePaginator.
// I should just print the queries executed.
DB::enableQueryLog();
$service->getFilteredSpareparts(['condition' => 'Rusak']);
print_r(DB::getQueryLog());
