<?php

namespace Modules\Payment\Actions;

use Modules\Order\Enums\OrderStatusEnum;
use Modules\Payment\Adapters\StripeWebhookAdapter;
use Modules\Payment\Enums\PaymentStatusEnum;
use Modules\Payment\Models\Payment;

class HandleStripeWebhookAction
{
    public function __construct(
        private StripeWebhookAdapter $adapter,
    ) {}
    public function execute(array $event): void
    {
        $data = $this->adapter->extractPaymentData($event);

        $payment = Payment::where('transaction_id', $data['transaction_id'])->first();

        if (!$payment) return;

        // Idempotency: skip if already processed
        if ($payment->status === PaymentStatusEnum::PAID) return;

        $payment->update([
            'status' => $data['status'],
            'gateway_response' => array_merge(
                $payment->gateway_response ?? [],
                $data['raw']
            ),
            'paid_at' => $data['status'] === PaymentStatusEnum::PAID ? now() :
                null,
        ]);
        // Update order status
        if ($data['status'] === PaymentStatusEnum::PAID) {
            $payment->order->update(['status' => OrderStatusEnum::CONFIRMED]);
        } elseif ($data['status'] === PaymentStatusEnum::FAILED) {
            $payment->order->update(['status' => OrderStatusEnum::CANCELLED]);
        }
    }
}
