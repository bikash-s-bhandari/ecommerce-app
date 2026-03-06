<?php

namespace Modules\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Payment\Jobs\ProcessStripeWebhookJob;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');
        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (SignatureVerificationException $e) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }
        $handled = ['payment_intent.succeeded', 'payment_intent.payment_failed'];
        if (in_array($event->type, $handled)) {
            ProcessStripeWebhookJob::dispatch($event->toArray())->onQueue('payments');
        }
        return response()->json(['received' => true]);
    }
}
