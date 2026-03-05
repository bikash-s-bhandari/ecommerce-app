<?php

use Illuminate\Support\Facades\Route;
use Modules\Catalog\Http\Controllers\CategoryController;
use Modules\Catalog\Http\Controllers\ProductController;
use Modules\Catalog\Http\Controllers\ProductImageController;


Route::prefix('v1')->group(function () {
    // Public product routes
    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{slug}', [ProductController::class, 'show']);
    // Public category routes
    Route::get('categories', [CategoryController::class, 'index']);
    // Admin-only routes
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::post('products', [ProductController::class, 'store']);
        Route::put('products/{id}', [ProductController::class, 'update']);
        Route::delete('products/{id}', [ProductController::class, 'destroy']);
        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{category}', [CategoryController::class, 'update']);
        Route::delete('categories/{category}', [CategoryController::class, 'destroy']);

        // Product image management
        Route::post('products/{id}/images',              [ProductImageController::class, 'upload']);
        Route::patch('products/{productId}/images/{image}/primary', [ProductImageController::class, 'setPrimary']);
        Route::delete('products/images/{image}',         [ProductImageController::class, 'destroy']);

        // Category image
        Route::post('categories/{category}/image',       [CategoryController::class, 'uploadImage']);
    });
});
