<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\AdminDashboardController;
use Modules\Admin\Http\Controllers\AdminOrderController;
use Modules\Admin\Http\Controllers\AdminUserController;

Route::prefix('v1/admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('dashboard', [AdminDashboardController::class, 'index']);
    // Orders
    Route::get('orders', [AdminOrderController::class, 'index']);
    Route::get('orders/{id}', [AdminOrderController::class, 'show']);
    Route::patch('orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
    // Users
    Route::get('users', [AdminUserController::class, 'index']);
    Route::patch('users/{id}/status', [AdminUserController::class, 'updateStatus']);
});
