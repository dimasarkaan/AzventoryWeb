<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$start = now()->subDays(6)->startOfDay();
$end = now()->endOfDay();

$service = app(\App\Services\DashboardService::class);
try {
    $data = $service->getStockMovements($start, $end);
    echo "SUCCESS:\n";
    print_r($data);
} catch (\Exception $e) {
    echo "ERROR:\n";
    echo $e->getMessage()."\n";
    echo $e->getTraceAsString()."\n";
}
