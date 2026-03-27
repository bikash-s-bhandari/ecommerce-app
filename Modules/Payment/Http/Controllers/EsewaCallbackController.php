<?php

namespace Modules\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Payment\Actions\VerifyPaymentAction;
use Modules\Payment\DTOs\PaymentVerifyDTO;
use Modules\Payment\Exceptions\PaymentFailedException;

/**
 * Handles the redirect callback from eSewa after payment.
 *
 * eSewa sends:  GET /payments/esewa/callback?data=<base64-JSON>
 *
 * The data param contains: transaction_uuid, total_amount, status,
 * signed_field_names, signature, etc. – all base64+JSON encoded.
 */
class EsewaCallbackController extends Controller
{
    public function __construct(
        private VerifyPaymentAction $verifyPaymentAction,
    ) {}

    public function handle(Request $request)
    {
        try {
            $payment = $this->verifyPaymentAction->execute(new PaymentVerifyDTO(
                gatewayRef:   $request->query('transaction_uuid', ''),
                callbackData: $request->only('data'),
            ));

            return response()->json([
                'message'    => 'Payment verified successfully.',
                'payment_id' => $payment->id,
                'status'     => $payment->status->value,
                'order_id'   => $payment->order_id,
            ]);
        } catch (PaymentFailedException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
