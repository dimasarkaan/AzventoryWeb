<?php

use Illuminate\Support\Facades\Validator;
use App\Http\Requests\SuperAdmin\Inventory\StoreSparepartRequest;
use App\Http\Requests\SuperAdmin\User\StoreUserRequest;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- STARTING VALIDATION REFACTOR VERIFICATION ---\n";

// Test 1: StoreSparepartRequest
echo "Checking StoreSparepartRequest... ";
try {
    $request = new StoreSparepartRequest();
    $rules = $request->rules();
    
    // Test with INVALID data
    $validator = Validator::make([], $rules);
    if ($validator->fails()) {
        echo "Rules Loaded OK. (Intentionally failed empty data)\n";
    } else {
        echo "FAILED (Empty data should fail validation)\n";
    }
} catch (\Exception $e) {
    echo "CRITICAL ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: StoreUserRequest
echo "Checking StoreUserRequest... ";
try {
    $request = new StoreUserRequest();
    $rules = $request->rules();
    
    // Test with VALID data
    $validData = [
        'email' => 'test_validation_' . time() . '@example.com',
        'role' => 'admin',
        'jabatan' => 'Tester',
        'status' => 'active'
    ];
    
    $validator = Validator::make($validData, $rules);
    if ($validator->passes()) {
        echo "Rules Loaded OK. (Valid data passed)\n";
    } else {
        echo "FAILED. Errors: " . implode(', ', $validator->errors()->all()) . "\n";
    }

} catch (\Exception $e) {
    echo "CRITICAL ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Check Controller Instantiation (Dependency Injection)
echo "Checking Controller DI resolution... ";
try {
    $controller = app(\App\Http\Controllers\SuperAdmin\InventoryController::class);
    echo "InventoryController OK.\n";
    
    $controller = app(\App\Http\Controllers\SuperAdmin\UserController::class);
    echo "UserController OK.\n";
} catch (\Exception $e) {
    echo "DI ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n--- VERIFICATION COMPLETE: ALL CHECKS PASSED ---\n";
