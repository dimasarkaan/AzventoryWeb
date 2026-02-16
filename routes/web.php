<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', \App\Http\Controllers\DashboardRedirectController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

    // Protected SuperAdmin Routes
    Route::middleware(['auth', 'verified', 'role:superadmin'])->group(function () {
        // Dashboard
        Route::get('dashboard/superadmin', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.superadmin');

        Route::get('reports/download', [\App\Http\Controllers\ReportController::class, 'download'])->name('reports.download');
        Route::get('reports', [\App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
        
        // Activity Logs
        Route::get('activity-logs/export', [\App\Http\Controllers\ActivityLogController::class, 'export'])->name('activity-logs.export');
        Route::get('activity-logs', [\App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
        
        // Stock Approvals (Strict Superadmin)
        Route::resource('stock-approvals', \App\Http\Controllers\StockApprovalController::class)
            ->only(['index', 'update', 'destroy'])
            ->parameters(['stock-approvals' => 'stock_log']);
    });

    // User Management (Strict Superadmin) - Moved out of superadmin prefix
    Route::middleware(['auth', 'verified', 'role:superadmin'])->group(function () {
        Route::patch('users/{user}/reset-password', [\App\Http\Controllers\UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('users/bulk-restore', [\App\Http\Controllers\UserController::class, 'bulkRestore'])->name('users.bulk-restore');
        Route::delete('users/bulk-force-delete', [\App\Http\Controllers\UserController::class, 'bulkForceDelete'])->name('users.bulk-force-delete');
        Route::patch('users/{id}/restore', [\App\Http\Controllers\UserController::class, 'restore'])->name('users.restore');
        Route::delete('users/{id}/force-delete', [\App\Http\Controllers\UserController::class, 'forceDelete'])->name('users.force-delete');
        Route::resource('users', \App\Http\Controllers\UserController::class);
    });

// SHARED ROUTES (Superadmin, Admin, Operator) - Prefix changed to 'inventory' for better semantics
Route::middleware(['auth', 'verified', 'role:superadmin,admin,operator'])->prefix('inventory')->name('inventory.')->group(function () {
    Route::view('/scan-qr', 'inventory.scan-qr')->name('scan-qr'); // Scan QR is useful for everyone

 

    Route::get('/check-part-number', [\App\Http\Controllers\Inventory\InventoryController::class, 'checkPartNumber'])->name('check-part-number');
    
    // Inventory Requests & Borrowing
    Route::post('/{sparepart}/stock-request', [\App\Http\Controllers\Inventory\StockRequestController::class, 'store'])->name('stock.request.store');
    Route::post('/{sparepart}/borrow', [\App\Http\Controllers\Inventory\BorrowingController::class, 'store'])->name('borrow.store');
    
    // Return borrowing
    Route::post('/borrow/{borrowing}/return', [\App\Http\Controllers\Inventory\BorrowingController::class, 'returnItem'])->name('borrow.return');
    Route::get('/borrow/{borrowing}/history', [\App\Http\Controllers\Inventory\BorrowingController::class, 'history'])->name('borrow.history');
    Route::get('/borrow/{borrowing}', [\App\Http\Controllers\Inventory\BorrowingController::class, 'show'])->name('borrow.show');
    
    // Soft Deletes (Inventory) - Authorization handled in Controller via Policy
    Route::post('/bulk-restore', [\App\Http\Controllers\Inventory\InventoryController::class, 'bulkRestore'])->name('bulk-restore');
    Route::delete('/bulk-force-delete', [\App\Http\Controllers\Inventory\InventoryController::class, 'bulkForceDelete'])->name('bulk-force-delete');
    Route::patch('/{id}/restore', [\App\Http\Controllers\Inventory\InventoryController::class, 'restore'])->name('restore');
    Route::delete('/force-delete-all', [\App\Http\Controllers\Inventory\InventoryController::class, 'forceDeleteAll'])->name('force-delete-all');
    Route::delete('/{id}/force-delete', [\App\Http\Controllers\Inventory\InventoryController::class, 'forceDelete'])->name('force-delete');
    
    // QR Code
    Route::get('/{inventory}/qr-code/download', [\App\Http\Controllers\Inventory\InventoryController::class, 'downloadQrCode'])->name('qr.download');
    Route::get('/{inventory}/qr-code/print', [\App\Http\Controllers\Inventory\InventoryController::class, 'printQrCode'])->name('qr.print');

    Route::resource('/', \App\Http\Controllers\Inventory\InventoryController::class)->parameters(['' => 'inventory']);
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:operator'])->prefix('operator')->name('operator.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Operator\DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'password.changed'])->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/my-inventory', [ProfileController::class, 'myInventory'])->name('profile.inventory');
    
    // Allow users to return their own items (uses SuperAdmin controller logic with added auth check)
    Route::post('/my-inventory/return/{borrowing}', [\App\Http\Controllers\Inventory\BorrowingController::class, 'returnItem'])->name('profile.inventory.return');

    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    
    // Change Password Routes
    Route::get('/change-password', [ChangePasswordController::class, 'create'])->name('password.change');
    Route::post('/change-password', [ChangePasswordController::class, 'store'])->name('password.change.store');

    // Global Search
    Route::get('/global-search', \App\Http\Controllers\GlobalSearchController::class)->name('global-search');
});

require __DIR__.'/auth.php';


