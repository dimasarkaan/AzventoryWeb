<?php

use App\Http\Controllers\Inventory\Api\InventoryController;
use Illuminate\Support\Facades\Route;

// API Routes

// Authentication (Public)
Route::middleware('throttle:5,1')->prefix('v1')->group(function () {
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login'])->name('api.login');
});

Route::middleware(['auth:sanctum', 'throttle:api', 'user.active', 'password.changed', \App\Http\Middleware\ApiVersion::class])->prefix('v1')->group(function () {
    // Inventory
    Route::middleware('token.ability:inventory')->group(function () {
        Route::apiResource('inventory', InventoryController::class)->names('api.inventory');
        Route::get('/inventory/{id}/logs', [InventoryController::class, 'logs'])->name('api.inventory.logs');
        Route::put('/inventory/{id}/adjust-stock', [InventoryController::class, 'adjustStock'])->middleware('throttle:api')->name('api.inventory.adjust-stock');
    });

    // Authentication (Protected)
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])->name('api.logout');

    // Borrowing
    Route::middleware('token.ability:borrowing')->group(function () {
        Route::get('/borrowings', [\App\Http\Controllers\Inventory\Api\BorrowingController::class, 'index'])->name('api.borrowings.index');
        Route::post('/inventory/{sparepart}/borrow', [\App\Http\Controllers\Inventory\Api\BorrowingController::class, 'store'])->name('api.borrowings.store');
        Route::get('/borrowings/{borrowing}', [\App\Http\Controllers\Inventory\Api\BorrowingController::class, 'show'])->name('api.borrowings.show');
        Route::post('/borrowings/{borrowing}/return', [\App\Http\Controllers\Inventory\Api\BorrowingController::class, 'returnItem'])->name('api.borrowings.return');
    });

    // Profile
    Route::get('/me', [\App\Http\Controllers\Inventory\Api\ProfileController::class, 'me'])->name('api.me');
    Route::put('/me', [\App\Http\Controllers\Inventory\Api\ProfileController::class, 'update'])->name('api.me.update');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Notifications\NotificationController::class, 'index'])->name('api.notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\Notifications\NotificationController::class, 'markAsRead'])->name('api.notifications.read');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Notifications\NotificationController::class, 'markAllAsRead'])->name('api.notifications.mark-all-read');

    // Users Management (Superadmin Only protected in Controller)
    Route::middleware('token.ability:user')->group(function () {
        Route::apiResource('users', \App\Http\Controllers\Inventory\Api\UserController::class)->names('api.users');
        Route::post('/users/{id}/reset-password', [\App\Http\Controllers\Inventory\Api\UserController::class, 'resetPassword'])->name('api.users.reset-password');
    });

    // Activity Logs & Stats
    Route::middleware('token.ability:log')->group(function () {
        Route::get('/activity-logs', [\App\Http\Controllers\Inventory\Api\ActivityLogController::class, 'index'])->name('api.activity-logs.index');
        Route::get('/activity-logs/user/{id}', [\App\Http\Controllers\Inventory\Api\ActivityLogController::class, 'userLogs'])->name('api.activity-logs.user');

        // Stats
        Route::get('/stats', [\App\Http\Controllers\Inventory\Api\StatsController::class, 'index'])->name('api.stats.index');
    });

    // Master Data (Full CRUD via API) - Khusus Superadmin
    Route::middleware(['role:superadmin'])->group(function () {
        Route::apiResource('brands', \App\Http\Controllers\Inventory\BrandController::class)->except('show')->names('api.brands')->middleware('token.ability:brand');
        Route::apiResource('categories', \App\Http\Controllers\Inventory\CategoryController::class)->except('show')->names('api.categories')->middleware('token.ability:category');
        Route::apiResource('locations', \App\Http\Controllers\Inventory\LocationController::class)->except('show')->names('api.locations')->middleware('token.ability:location');
    });
});
