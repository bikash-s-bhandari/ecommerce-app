<?php

namespace Modules\Payment\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Payment\Actions\VerifyPaymentAction;
use Modules\Payment\DTOs\PaymentVerifyDTO;
use Modules\Payment\Exceptions\PaymentFailedException;

/**
 * Handles the redirect callback from Khalti after payment.
 *
 * Khalti sends:
 *   GET /payments/khalti/callback
 *       ?pidx=<pidx>
 *       &status=Completed
 *       &transaction_id=<...>
 *       &tidx=<...>
 *       &amount=<paisa>
 *       &mobile=<...>
 *       &purchase_order_id=ORD-123
 *
 * We use the pidx to call the Khalti lookup API for final verification.
 * Never trust the 'status' param in the URL alone – always verify server-side.
 */
class KhaltiCallbackController extends Controller
{
    public function __construct(
        private VerifyPaymentAction $verifyPaymentAction,
    ) {}

    public function handle(Request $request)
    {
        try {
            $pidx = $request->query('pidx');

            if (! $pidx) {
                return response()->json(['message' => 'Missing pidx parameter.'], 422);
            }

            $payment = $this->verifyPaymentAction->execute(new PaymentVerifyDTO(
                gatewayRef:   $pidx,
                callbackData: $request->query(),
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
