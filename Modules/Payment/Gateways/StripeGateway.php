<?php

namespace Modules\Payment\Gateways;

use App\Exceptions\BusinessException;
use Modules\Payment\Contracts\PaymentGatewayInterface;
use Modules\Payment\DTOs\ProcessPaymentDTO;
use Stripe\StripeClient;

class StripeGateway implements PaymentGatewayInterface
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }
    public function createIntent(ProcessPaymentDTO $dto): array
    {
        try {
            $intent = $this->stripe->paymentIntents->create([
                'amount' => (int) ($dto->amount * 100), // convert to cents
                'currency' => $dto->currency,
                'payment_method' => $dto->paymentMethodId,
                'confirm' => true,
                'return_url' => config('app.url') . '/payment/return',
                'metadata' => ['order_id' => $dto->orderId],
                'automatic_payment_methods' => ['enabled' => true, 'allow_redirects' =>
                'never'],
            ]);
            return [
                'id' => $intent->id,
                'status' => $intent->status,
                'client_secret' => $intent->client_secret,
                'raw' => $intent->toArray(),
            ];
        } catch (\Stripe\Exception\CardException $e) {
            throw new BusinessException('Payment failed: ' . $e->getError()->message, 402);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new BusinessException('Payment gateway error. Please try again.', 500);
        }
    }

    public function confirm(string $paymentIntentId): array
    {
        $intent = $this->stripe->paymentIntents->retrieve($paymentIntentId);
        return $intent->toArray();
    }
    public function refund(string $transactionId, float $amount): bool
    {
        $this->stripe->refunds->create([
            'payment_intent' => $transactionId,
            'amount' => (int) ($amount * 100),
        ]);
        return true;
    }
}
