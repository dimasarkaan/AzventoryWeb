<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Inventory\Api\InventoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan rute API untuk aplikasi Anda. Rute-rute ini
| dimuat oleh RouteServiceProvider dalam grup yang ditetapkan ke grup middleware
| "api". Nikmati membangun API Anda!
|
*/


Route::middleware(['auth:sanctum', 'throttle:60,1'])->prefix('v1')->group(function () {
    Route::apiResource('inventory', InventoryController::class)->names('api.inventory');
    Route::post('/inventory/{id}/adjust-stock', [InventoryController::class, 'adjustStock']);
});
