<?php

namespace Modules\Payment\Actions;

use Modules\Payment\Contracts\PaymentGatewayInterface;
use Modules\Payment\DTOs\ProcessPaymentDTO;
use Modules\Payment\Enums\PaymentStatusEnum;
use Modules\Payment\Models\Payment;

class ProcessPaymentAction
{
    public function __construct(
        private PaymentGatewayInterface $gateway,
    ) {}

    public function execute(ProcessPaymentDTO $dto): Payment
    {
        $payment = Payment::create([
            'order_id' => $dto->orderId,
            'status' => PaymentStatusEnum::PENDING,
            'gateway' => 'stripe',
            'amount' => $dto->amount,
            'currency' => $dto->currency,
        ]);

        $result = $this->gateway->createIntent($dto);

        $payment->update([
            'transaction_id' => $result['id'],
            'status' => $result['status'] === 'succeeded' ?
                PaymentStatusEnum::PAID : PaymentStatusEnum::PENDING,
            'gateway_response' => $result['raw'],
            'paid_at' => $result['status'] === 'succeeded' ? now() : null,
        ]);

        return $payment;
    }
}
