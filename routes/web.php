<?php

use Illuminate\Support\Facades\Route;

// Controller Fitur
use App\Http\Controllers\Dashboard\DashboardRedirectController;
use App\Http\Controllers\Dashboard\SuperAdminDashboardController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\OperatorDashboardController;
use App\Http\Controllers\Reports\ReportController;
use App\Http\Controllers\Reports\ActivityLogController;
use App\Http\Controllers\Inventory\StockApprovalController;
use App\Http\Controllers\Inventory\InventoryController;
use App\Http\Controllers\Inventory\StockRequestController;
use App\Http\Controllers\Inventory\BorrowingController;
use App\Http\Controllers\Users\UserController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Controllers\General\GlobalSearchController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\ApiTokenController;

/*
|--------------------------------------------------------------------------
|
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Fitur: Dashboard, Inventaris, Laporan, Pengguna, dll.
| Gunakan penamaan konsisten: 'fitur.aksi'
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Rute Autentikasi
require __DIR__.'/auth.php';

// --- Pengalihan Dashboard ---
Route::get('/dashboard', DashboardRedirectController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'verified', 'password.changed'])->group(function () {

    // --- Fitur Dashboard ---
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        // SuperAdmin
        Route::get('/superadmin', [SuperAdminDashboardController::class, 'index'])
            ->middleware('role:superadmin')
            ->name('superadmin');

        // Endpoint AJAX: data pergerakan stok per-widget (Opsi C)
        Route::get('/movement-data', [SuperAdminDashboardController::class, 'movementData'])
            ->middleware('role:superadmin')
            ->name('movement-data');
            
        // Admin
        Route::get('/admin', [AdminDashboardController::class, 'index'])
            ->middleware('role:superadmin,admin')
            ->name('admin');

        // Endpoint AJAX: data pergerakan stok per-widget untuk Admin
        Route::get('/admin/movement-data', [AdminDashboardController::class, 'movementData'])
            ->middleware('role:superadmin,admin')
            ->name('admin.movement-data');


        // Operator
        Route::get('/operator', [OperatorDashboardController::class, 'index'])
            ->middleware('role:superadmin,admin,operator')
            ->name('operator');
    });

    // --- Manajemen Inventaris & Stok (Berbagi: Superadmin, Admin, Operator) ---
    Route::prefix('inventory')->name('inventory.')->middleware('role:superadmin,admin,operator')->group(function () {
        
        // Inventaris Umum
        Route::view('/scan-qr', 'inventory.scan-qr')->name('scan-qr');
        Route::get('/check-part-number', [InventoryController::class, 'checkPartNumber'])->name('check-part-number');
        
        // Persetujuan Stok (Superadmin & Admin) 
        Route::resource('stock-approvals', StockApprovalController::class)
            ->middleware('role:superadmin,admin')
            ->only(['index', 'update', 'destroy'])
            ->parameters(['stock-approvals' => 'stock_log']);

        // Kode QR
        Route::get('/{inventory}/qr-code/download', [InventoryController::class, 'downloadQrCode'])->name('qr.download');
        Route::get('/{inventory}/qr-code/print', [InventoryController::class, 'printQrCode'])->name('qr.print');

        // Soft Deletes (Letakkan sebelum resource utama karena 'bulk' bisa dianggap ID)
        Route::post('/bulk-restore', [InventoryController::class, 'bulkRestore'])->name('bulk-restore');
        Route::delete('/bulk-force-delete', [InventoryController::class, 'bulkForceDelete'])->name('bulk-force-delete');
        Route::delete('/force-delete-all', [InventoryController::class, 'forceDeleteAll'])->name('force-delete-all');
        
        // Rute spesifik ID lainnya
        Route::patch('/{id}/restore', [InventoryController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [InventoryController::class, 'forceDelete'])->name('force-delete');

        // Permintaan Stok & Peminjaman (Aksi)
        Route::post('/{sparepart}/stock-request', [StockRequestController::class, 'store'])->name('stock.request.store');
        Route::post('/{sparepart}/borrow', [BorrowingController::class, 'store'])->name('borrow.store');
        
        // Pengembalian & Riwayat
        Route::post('/borrow/{borrowing}/return', [BorrowingController::class, 'returnItem'])->name('borrow.return');
        Route::get('/borrow/{borrowing}/history', [BorrowingController::class, 'history'])->name('borrow.history');
        Route::get('/borrow/{borrowing}', [BorrowingController::class, 'show'])->name('borrow.show');

        // Resource Utama (Letakkan di bawah rute spesifik agar tidak tertimpa wildcard)
        Route::resource('/', InventoryController::class)->parameters(['' => 'inventory']);
    });

    // --- Laporan & Analitik ---
    Route::prefix('reports')->name('reports.')->group(function () {
        // Laporan Umum (Superadmin & Admin)
        Route::middleware('role:superadmin,admin')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/download', [ReportController::class, 'download'])->name('download');
        });

        // Log Aktivitas (Personal untuk Operator, Global untuk Superadmin/Admin)
        Route::middleware('role:superadmin,admin,operator')->group(function () {
            Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
            Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
        });
    });

    // --- Manajemen Pengguna (Superadmin) ---
    Route::prefix('users')->name('users.')->middleware('role:superadmin')->group(function () {
        Route::patch('/{user}/reset-password', [UserController::class, 'resetPassword'])->name('reset-password');
        Route::post('/bulk-restore', [UserController::class, 'bulkRestore'])->name('bulk-restore');
        Route::delete('/bulk-force-delete', [UserController::class, 'bulkForceDelete'])->name('bulk-force-delete');
        Route::patch('/{id}/restore', [UserController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force-delete', [UserController::class, 'forceDelete'])->name('force-delete');
        
        Route::resource('/', UserController::class)->parameters(['' => 'user']);
    });

    // --- Profil & Pengaturan (Semua Terautentikasi) ---
    Route::group([], function () {
        // Profil
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::get('/my-inventory', [ProfileController::class, 'myInventory'])->name('profile.inventory');
        
        // API Tokens
        Route::post('/profile/api-tokens', [ApiTokenController::class, 'store'])->name('profile.api-tokens.store');
        Route::delete('/profile/api-tokens/{tokenId}', [ApiTokenController::class, 'destroy'])->name('profile.api-tokens.destroy');
        
        // Kembalikan item sendiri
        Route::post('/my-inventory/return/{borrowing}', [BorrowingController::class, 'returnItem'])->name('profile.inventory.return');

        // Notifikasi
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
        
        // Ganti Password
        Route::get('/change-password', [ChangePasswordController::class, 'create'])->name('password.change');
        Route::post('/change-password', [ChangePasswordController::class, 'store'])->name('password.change.store');

        // Pencarian Global
        Route::get('/global-search', GlobalSearchController::class)->name('global-search');
    });
});
