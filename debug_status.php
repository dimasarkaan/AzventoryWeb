<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$borrowing = App\Models\Borrowing::with('returns')
    ->where('status', 'borrowed')
    ->latest()
    ->first();

if ($borrowing) {
    echo "ID: " . $borrowing->id . "\n";
    echo "Qty: " . $borrowing->quantity . "\n";
    echo "Returns Count: " . $borrowing->returns->count() . "\n";
    echo "Returns Sum: " . $borrowing->returns->sum('quantity') . "\n";
    echo "Remaining (Accessor): " . $borrowing->remaining_quantity . "\n";
} else {
    echo "No active borrowings found.\n";
}
