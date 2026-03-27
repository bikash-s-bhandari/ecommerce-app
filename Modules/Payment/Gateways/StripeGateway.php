<?php

namespace Modules\Payment\Gateways;

use Modules\Payment\Contracts\PaymentGatewayInterface;
use Modules\Payment\DTOs\PaymentInitiateDTO;
use Modules\Payment\DTOs\PaymentInitiateResultDTO;
use Modules\Payment\DTOs\PaymentVerifyDTO;
use Modules\Payment\DTOs\PaymentVerifyResultDTO;
use Modules\Payment\Enums\PaymentStatusEnum;
use Modules\Payment\Exceptions\PaymentFailedException;
use Stripe\StripeClient;

/**
 * Stripe uses a client-side flow:
 *   1. initiate()  → creates PaymentIntent, returns clientSecret
 *   2. Frontend uses Stripe.js to confirm with the clientSecret
 *   3. Stripe sends a webhook (handled separately by StripeWebhookController)
 *   4. verify()    → retrieves PaymentIntent status (for manual/sync checks)
 *
 * LSP compliance:
 *   - initiate() always returns PaymentInitiateResultDTO (type='intent')
 *   - verify()   always returns PaymentVerifyResultDTO
 *   - errors     always throw PaymentFailedException
 */
class StripeGateway implements PaymentGatewayInterface
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('payment.gateways.stripe.secret'));
    }

    public function initiate(PaymentInitiateDTO $dto): PaymentInitiateResultDTO
    {
        try {
            $intent = $this->stripe->paymentIntents->create([
                'amount'                    => (int) ($dto->amount * 100),
                'currency'                  => $dto->currency,
                'payment_method'            => $dto->paymentMethodId,
                'confirm'                   => true,
                'return_url'                => $dto->returnUrl,
                'metadata'                  => ['order_id' => $dto->orderId],
                'automatic_payment_methods' => ['enabled' => true, 'allow_redirects' => 'never'],
            ]);

            return new PaymentInitiateResultDTO(
                type:         'intent',
                gatewayRef:   $intent->id,
                redirectUrl:  null,
                clientSecret: $intent->client_secret,
                formData:     [],
                raw:          $intent->toArray(),
            );
        } catch (\Stripe\Exception\CardException $e) {
            throw new PaymentFailedException('Card error: ' . $e->getError()->message, 402, $e);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new PaymentFailedException('Stripe gateway error. Please try again.', 500, $e);
        }
    }

    public function verify(PaymentVerifyDTO $dto): PaymentVerifyResultDTO
    {
        try {
            $intent = $this->stripe->paymentIntents->retrieve($dto->gatewayRef);

            return new PaymentVerifyResultDTO(
                status:     $this->mapStatus($intent->status),
                gatewayRef: $intent->id,
                amount:     $intent->amount / 100,
                currency:   $intent->currency,
                raw:        $intent->toArray(),
            );
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new PaymentFailedException('Failed to verify Stripe payment.', 500, $e);
        }
    }

    public function refund(string $transactionId, float $amount): bool
    {
        try {
            $this->stripe->refunds->create([
                'payment_intent' => $transactionId,
                'amount'         => (int) ($amount * 100),
            ]);
            return true;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            throw new PaymentFailedException('Stripe refund failed: ' . $e->getMessage(), 500, $e);
        }
    }

    private function mapStatus(string $stripeStatus): PaymentStatusEnum
    {
        return match ($stripeStatus) {
            'succeeded'              => PaymentStatusEnum::PAID,
            'canceled'               => PaymentStatusEnum::FAILED,
            'requires_payment_method' => PaymentStatusEnum::FAILED,
            default                  => PaymentStatusEnum::PENDING,
        };
    }
}
