<?php

use App\Services\InventoryService;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate Auth
$user = User::where('role', 'superadmin')->first() ?? User::first();
auth()->login($user);

echo "--- STARTING VERIFICATION ---\n";
echo "User: " . $user->name . "\n";

$service = app(InventoryService::class);

// 1. Test Create
echo "\n[TEST 1] Create Sparepart... ";
$data = [
    'name' => 'Refactor Test Unit',
    'part_number' => 'TEST-' . time(),
    'brand' => 'TestBrand',
    'category' => 'TestCat',
    'location' => 'TestLoc',
    'condition' => 'Baru',
    'type' => 'asset',
    'stock' => 5,
    'status' => 'aktif',
    // Skip image for simple backend test, focus on logic
];

try {
    $result = $service->createSparepart($data);
    $sparepart = $result['data'];
    echo "OK (ID: {$sparepart->id})\n";
} catch (\Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Verify QR Generation
echo "[TEST 2] Verify QR Code Path... ";
if ($sparepart->qr_code_path && Storage::disk('public')->exists($sparepart->qr_code_path)) {
    echo "OK (Path: {$sparepart->qr_code_path})\n";
} else {
    echo "FAILED (Path missing or file not found)\n";
    // Don't exit, try to continue
}

// 3. Test Soft Delete
echo "[TEST 3] Soft Delete... ";
try {
    $service->deleteSparepart($sparepart);
    if ($sparepart->fresh()->trashed()) {
        echo "OK\n";
    } else {
        echo "FAILED (Item not trashed)\n";
    }
} catch (\Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}

// 4. Test Restore
echo "[TEST 4] Restore... ";
try {
    $service->restoreSparepart($sparepart->id);
    if (!$sparepart->fresh()->trashed()) {
        echo "OK\n";
    } else {
        echo "FAILED (Item still trashed)\n";
    }
} catch (\Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}

// 5. Test Force Delete
echo "[TEST 5] Force Delete... ";
try {
    // Delete again first
    $service->deleteSparepart($sparepart);
    $service->forceDeleteSparepart($sparepart->id);
    
    if (\App\Models\Sparepart::withTrashed()->find($sparepart->id) === null) {
        echo "OK\n";
    } else {
        echo "FAILED (Item still exists in DB)\n";
    }
} catch (\Exception $e) {
    echo "FAILED: " . $e->getMessage() . "\n";
}

echo "\n--- VERIFICATION COMPLETE ---\n";
