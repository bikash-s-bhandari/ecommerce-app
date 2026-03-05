<?php

use Illuminate\Support\Facades\Route;
use Modules\Cart\Http\Controllers\CartController;

Route::prefix('v1/cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('items', [CartController::class, 'addItem']);
    Route::patch('items/{item}', [CartController::class, 'updateItem']);
    Route::delete('items/{item}', [CartController::class, 'removeItem']);
    Route::delete('/', [CartController::class, 'clear']);
});
