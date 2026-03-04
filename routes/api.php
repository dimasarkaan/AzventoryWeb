<?php

use App\Http\Controllers\Inventory\Api\InventoryController;
use Illuminate\Support\Facades\Route;

// API Routes

Route::middleware(['auth:sanctum', 'throttle:60,1'])->prefix('v1')->group(function () {
    Route::apiResource('inventory', InventoryController::class)->names('api.inventory');
    Route::put('/inventory/{id}/adjust-stock', [InventoryController::class, 'adjustStock'])->name('api.inventory.adjust-stock');
});
