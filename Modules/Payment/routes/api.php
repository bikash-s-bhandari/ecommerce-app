<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\StripeWebhookController;

// Stripe sends raw body - exempt from CSRF & JSON middleware
Route::post('v1/payments/stripe/webhook', [StripeWebhookController::class, 'handle'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
