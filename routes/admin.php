<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

// Admin Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
