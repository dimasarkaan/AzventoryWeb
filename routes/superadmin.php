<?php

use App\Http\Controllers\SuperAdmin\DashboardController;
use App\Http\Controllers\SuperAdmin\InventoryController;
use App\Http\Controllers\SuperAdmin\StockRequestController;
use App\Http\Controllers\SuperAdmin\StockApprovalController;
use App\Http\Controllers\SuperAdmin\ActivityLogController;
use Illuminate\Support\Facades\Route;

// Super Admin Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('inventory', InventoryController::class);
Route::get('/inventory/{inventory}/qr/download', [InventoryController::class, 'downloadQrCode'])->name('inventory.qr.download');
Route::get('/inventory/{inventory}/qr/print', [InventoryController::class, 'printQrCode'])->name('inventory.qr.print');

// Stock Request Routes
Route::get('/inventory/{inventory}/stock/request', [StockRequestController::class, 'create'])->name('inventory.stock.request.create');
Route::post('/inventory/{inventory}/stock/request', [StockRequestController::class, 'store'])->name('inventory.stock.request.store');

// Stock Approval Routes
Route::get('/stock-approvals', [StockApprovalController::class, 'index'])->name('stock-approvals.index');
Route::patch('/stock-approvals/{stock_log}', [StockApprovalController::class, 'update'])->name('stock-approvals.update');
Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');