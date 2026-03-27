<?php

namespace Modules\Payment\Actions;

use Modules\Order\Enums\OrderStatusEnum;
use Modules\Payment\DTOs\PaymentVerifyDTO;
use Modules\Payment\Enums\PaymentStatusEnum;
use Modules\Payment\Factories\PaymentGatewayFactory;
use Modules\Payment\Models\Payment;

/**
 * Verifies a payment after the gateway redirects back (eSewa / Khalti).
 * Also used for manual Stripe verification if needed.
 *
 * The correct gateway to call is determined from the Payment record that was
 * saved during ProcessPaymentAction — so even if the user chose "esewa" and
 * the callback arrives later, we use EsewaGateway (not the config default).
 *
 * Flow:
 *   1. Callback arrives with gatewayRef (pidx / transaction_uuid / intentId)
 *   2. We find the Payment record by transaction_id
 *   3. We read payment->gateway to know which gateway to call
 *   4. Factory builds the right gateway instance
 *   5. verify() returns a consistent PaymentVerifyResultDTO (LSP)
 *   6. We update Payment + Order status
 */
class VerifyPaymentAction
{
    public function __construct(
        private PaymentGatewayFactory $factory,
    ) {}

    public function execute(PaymentVerifyDTO $dto): Payment
    {
        // Find the payment first so we know which gateway to use
        $payment = Payment::where('transaction_id', $dto->gatewayRef)->firstOrFail();

        // Idempotency: skip if already in a terminal state
        if ($payment->status === PaymentStatusEnum::PAID) {
            return $payment;
        }

        // Resolve the correct gateway from what was stored at initiation time
        // This is critical: if the user paid with eSewa, verify with eSewa
        $gateway = $this->factory->make($payment->gateway);

        // Returns consistent PaymentVerifyResultDTO regardless of gateway (LSP)
        $result = $gateway->verify($dto);

        $payment->update([
            'status'           => $result->status,
            'gateway_response' => array_merge($payment->gateway_response ?? [], $result->raw),
            'paid_at'          => $result->status === PaymentStatusEnum::PAID ? now() : null,
        ]);

        if ($result->status === PaymentStatusEnum::PAID) {
            $payment->order->update(['status' => OrderStatusEnum::CONFIRMED]);
        } elseif ($result->status === PaymentStatusEnum::FAILED) {
            $payment->order->update(['status' => OrderStatusEnum::CANCELLED]);
        }

        return $payment->fresh();
    }
}
