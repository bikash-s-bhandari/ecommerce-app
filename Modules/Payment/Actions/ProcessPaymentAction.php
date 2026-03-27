<?php

namespace Modules\Payment\Actions;

use Modules\Payment\DTOs\PaymentInitiateDTO;
use Modules\Payment\DTOs\PaymentInitiateResultDTO;
use Modules\Payment\Enums\PaymentStatusEnum;
use Modules\Payment\Factories\PaymentGatewayFactory;
use Modules\Payment\Models\Payment;

class ProcessPaymentAction
{
    public function __construct(
        private PaymentGatewayFactory $factory,
    ) {}

    /**
     * @return array{payment: Payment, result: PaymentInitiateResultDTO}
     */
    public function execute(PaymentInitiateDTO $dto): array
    {
        // Resolve the gateway the user chose in this request
        $gateway = $this->factory->make($dto->gateway);

        $payment = Payment::create([
            'order_id' => $dto->orderId,
            'status'   => PaymentStatusEnum::PENDING,
            'gateway'  => $dto->gateway,
            'amount'   => $dto->amount,
            'currency' => $dto->currency,
        ]);


        $result = $gateway->initiate($dto);


        $payment->update([
            'transaction_id'   => $result->gatewayRef,
            'gateway_response' => $result->raw,
        ]);

        return [
            'payment' => $payment,
            'result'  => $result,
        ];
    }
}
