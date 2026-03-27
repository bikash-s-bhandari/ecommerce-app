<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\EsewaCallbackController;
use Modules\Payment\Http\Controllers\KhaltiCallbackController;
use Modules\Payment\Http\Controllers\StripeWebhookController;

Route::post('v1/payments/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

Route::get('v1/payments/esewa/callback', [EsewaCallbackController::class, 'handle'])
    ->name('payment.esewa.callback');

Route::get('v1/payments/khalti/callback', [KhaltiCallbackController::class, 'handle'])
    ->name('payment.khalti.callback');
