<?php

use App\Http\Controllers\Operator\DashboardController;
use Illuminate\Support\Facades\Route;

// Operator Routes
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
