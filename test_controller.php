<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$borrowing = \App\Models\Borrowing::first();
if (!$borrowing) {
    echo "No borrowing found.\n";
    exit(1);
}
echo "Borrowing Class: " . get_class($borrowing) . "\n";

try {
    $controller = $app->make(\App\Http\Controllers\SuperAdmin\BorrowingController::class);
    $response = $controller->history($borrowing);

    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Content: " . substr($response->getContent(), 0, 500) . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
