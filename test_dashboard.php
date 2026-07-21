<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Auth::loginUsingId(3);
    $c = app(App\Http\Controllers\Dashboard\OperatorDashboardController::class);
    $output = $c->index()->render();
    echo "SUCCESS, length: " . strlen($output) . "\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
}
