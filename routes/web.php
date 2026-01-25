<?php

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role === 'superadmin') {
        return redirect()->route('superadmin.dashboard');
    } elseif ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'operator') {
        return redirect()->route('operator.dashboard');
    }
    abort(403, 'Unauthorized action.');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
    Route::view('/scan-qr', 'superadmin.scan-qr')->name('scan-qr');
    Route::patch('users/{user}/reset-password', [\App\Http\Controllers\SuperAdmin\UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::get('inventory/check-part-number', [\App\Http\Controllers\SuperAdmin\InventoryController::class, 'checkPartNumber'])->name('inventory.check-part-number');
    Route::get('reports/download', [\App\Http\Controllers\SuperAdmin\ReportController::class, 'download'])->name('reports.download');
    Route::get('reports', [\App\Http\Controllers\SuperAdmin\ReportController::class, 'index'])->name('reports.index');
    
    // Activity Logs
    Route::get('activity-logs', [\App\Http\Controllers\SuperAdmin\ActivityLogController::class, 'index'])->name('activity-logs.index');
    
    // Stock Approvals
    Route::resource('stock-approvals', \App\Http\Controllers\SuperAdmin\StockApprovalController::class)->only(['index', 'update', 'destroy']);
    
    // Inventory Requests & Borrowing (Nested or separate?)
    // Based on inspection, blade uses: superadmin.inventory.stock.request.store, superadmin.inventory.borrow.store
    // We likely need nested routes or specific named routes to match the Blade calls
    Route::post('inventory/{sparepart}/stock-request', [\App\Http\Controllers\SuperAdmin\StockRequestController::class, 'store'])->name('inventory.stock.request.store');
    Route::post('inventory/{sparepart}/borrow', [\App\Http\Controllers\SuperAdmin\BorrowingController::class, 'store'])->name('inventory.borrow.store');
    
    // Return borrowing (from blade: superadmin/inventory/borrow/{id}/return)
    Route::post('inventory/borrow/{borrowing}/return', [\App\Http\Controllers\SuperAdmin\BorrowingController::class, 'returnItem'])->name('inventory.borrow.return');
    
    Route::resource('users', \App\Http\Controllers\SuperAdmin\UserController::class);
    Route::resource('inventory', \App\Http\Controllers\SuperAdmin\InventoryController::class);
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:operator'])->prefix('operator')->name('operator.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Operator\DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/change-password', [ChangePasswordController::class, 'create'])->name('password.change');
    Route::post('/change-password', [ChangePasswordController::class, 'store'])->name('password.change.store');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Notification Routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
