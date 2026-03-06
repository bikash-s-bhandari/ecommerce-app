<?php

namespace Modules\Payment\Adapters;

use Modules\Payment\Enums\PaymentStatusEnum;

class StripeWebhookAdapter
{
    public function extractPaymentData(array $event): array
    {
        $intent = $event['data']['object'];

        return [
            'transaction_id' => $intent['id'],
            'status' => $this->mapStatus($intent['status']),
            'amount' => $intent['amount'] / 100,
            'currency' => $intent['currency'],
            'order_id' => $intent['metadata']['order_id'] ?? null,
            'raw' => $intent,
        ];
    }

    private function mapStatus(string $stripeStatus): PaymentStatusEnum
    {
        return match ($stripeStatus) {
            'succeeded' => PaymentStatusEnum::PAID,
            'payment_failed' => PaymentStatusEnum::FAILED,
            'canceled' => PaymentStatusEnum::FAILED,
            default => PaymentStatusEnum::PENDING,
        };
    }
}
