<?php

use App\Http\Controllers\Inventory\Api\InventoryController;
use Illuminate\Support\Facades\Route;

// API Routes

Route::middleware(['auth:sanctum', 'throttle:60,1'])->prefix('v1')->group(function () {
    // Inventory
    Route::apiResource('inventory', InventoryController::class)->names('api.inventory');
    Route::get('/inventory/{id}/logs', [InventoryController::class, 'logs'])->name('api.inventory.logs');
    Route::put('/inventory/{id}/adjust-stock', [InventoryController::class, 'adjustStock'])->name('api.inventory.adjust-stock');

    // Borrowing
    Route::get('/borrowings', [\App\Http\Controllers\Inventory\Api\BorrowingController::class, 'index'])->name('api.borrowings.index');
    Route::post('/borrowings', [\App\Http\Controllers\Inventory\Api\BorrowingController::class, 'store'])->name('api.borrowings.store');
    Route::get('/borrowings/{id}', [\App\Http\Controllers\Inventory\Api\BorrowingController::class, 'show'])->name('api.borrowings.show');
    Route::post('/borrowings/{id}/return', [\App\Http\Controllers\Inventory\Api\BorrowingController::class, 'returnItem'])->name('api.borrowings.return');

    // Profile
    Route::get('/me', [\App\Http\Controllers\Inventory\Api\ProfileController::class, 'me'])->name('api.me');
    Route::put('/me', [\App\Http\Controllers\Inventory\Api\ProfileController::class, 'update'])->name('api.me.update');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Notifications\NotificationController::class, 'index'])->name('api.notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Notifications\NotificationController::class, 'markAsRead'])->name('api.notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Notifications\NotificationController::class, 'markAllAsRead'])->name('api.notifications.mark-all-read');

    // Users Management (Superadmin Only protected in Controller)
    Route::apiResource('users', \App\Http\Controllers\Inventory\Api\UserController::class)->names('api.users');
    Route::post('/users/{id}/reset-password', [\App\Http\Controllers\Inventory\Api\UserController::class, 'resetPassword'])->name('api.users.reset-password');

    // Activity Logs
    Route::get('/activity-logs', [\App\Http\Controllers\Inventory\Api\ActivityLogController::class, 'index'])->name('api.activity-logs.index');
    Route::get('/activity-logs/user/{id}', [\App\Http\Controllers\Inventory\Api\ActivityLogController::class, 'userLogs'])->name('api.activity-logs.user');

    // Stats
    Route::get('/stats', [\App\Http\Controllers\Inventory\Api\StatsController::class, 'index'])->name('api.stats.index');

    // Master Data (Full CRUD via API)
    Route::apiResource('brands', \App\Http\Controllers\Inventory\BrandController::class)->names('api.brands');
    Route::apiResource('categories', \App\Http\Controllers\Inventory\CategoryController::class)->names('api.categories');
    Route::apiResource('locations', \App\Http\Controllers\Inventory\LocationController::class)->names('api.locations');
});
