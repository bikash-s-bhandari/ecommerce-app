<?php

namespace Modules\Payment\Gateways;

use Illuminate\Support\Facades\Http;
use Modules\Payment\Contracts\PaymentGatewayInterface;
use Modules\Payment\DTOs\PaymentInitiateDTO;
use Modules\Payment\DTOs\PaymentInitiateResultDTO;
use Modules\Payment\DTOs\PaymentVerifyDTO;
use Modules\Payment\DTOs\PaymentVerifyResultDTO;
use Modules\Payment\Enums\PaymentStatusEnum;
use Modules\Payment\Exceptions\PaymentFailedException;

/**
 * Khalti Payment Gateway (Nepal)
 *
 * Flow:
 *   1. initiate()  → calls Khalti API → gets pidx + payment_url
 *                    Returns PaymentInitiateResultDTO(type='redirect', redirectUrl=payment_url)
 *                    Backend (or frontend) redirects user to payment_url
 *
 *   2. Khalti redirects to return_url with ?pidx=...&status=Completed&...
 *
 *   3. verify()    → calls Khalti lookup API with pidx to confirm status
 *                    Returns PaymentVerifyResultDTO with PAID / FAILED
 *
 * Test credentials (sandbox):
 *   base_url   : https://a.khalti.com
 *   secret_key : <get from https://test-admin.khalti.com>
 *   test phone : 9800000000 ... 9800000005
 *   MPIN       : 1111  |  OTP: 987654
 *
 * LSP compliance:
 *   - initiate() always returns PaymentInitiateResultDTO (type='redirect')
 *   - verify()   always returns PaymentVerifyResultDTO
 *   - errors     always throw PaymentFailedException
 */
class KhaltiGateway implements PaymentGatewayInterface
{
    private string $secretKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('payment.gateways.khalti.secret_key');
        $this->baseUrl   = config('payment.gateways.khalti.base_url');
    }

    public function initiate(PaymentInitiateDTO $dto): PaymentInitiateResultDTO
    {
        $response = Http::withHeaders([
            'Authorization' => 'Key ' . $this->secretKey,
        ])->post($this->baseUrl . '/api/v2/epayment/initiate/', [
            'return_url'          => $dto->returnUrl . '?gateway=khalti',
            'website_url'         => config('app.url'),
            'amount'              => (int) ($dto->amount * 100),   // in paisa
            'purchase_order_id'   => 'ORD-' . $dto->orderId,
            'purchase_order_name' => 'Order #' . $dto->orderId,
        ]);

        if (! $response->successful()) {
            throw new PaymentFailedException(
                'Khalti initiation failed: ' . $response->json('detail', $response->body())
            );
        }

        $data = $response->json();

        return new PaymentInitiateResultDTO(
            type:         'redirect',
            gatewayRef:   $data['pidx'],
            redirectUrl:  $data['payment_url'],
            clientSecret: null,
            formData:     [],
            raw:          $data,
        );
    }

    public function verify(PaymentVerifyDTO $dto): PaymentVerifyResultDTO
    {
        // pidx comes from the callback query param OR from our stored gatewayRef
        $pidx = $dto->callbackData['pidx'] ?? $dto->gatewayRef;

        $response = Http::withHeaders([
            'Authorization' => 'Key ' . $this->secretKey,
        ])->post($this->baseUrl . '/api/v2/epayment/lookup/', [
            'pidx' => $pidx,
        ]);

        if (! $response->successful()) {
            throw new PaymentFailedException(
                'Khalti verification failed: ' . $response->json('detail', $response->body())
            );
        }

        $data = $response->json();

        $status = ($data['status'] ?? '') === 'Completed'
            ? PaymentStatusEnum::PAID
            : PaymentStatusEnum::FAILED;

        return new PaymentVerifyResultDTO(
            status:     $status,
            gatewayRef: $data['pidx'],
            amount:     ($data['total_amount'] ?? 0) / 100,   // convert paisa → NPR
            currency:   'NPR',
            raw:        $data,
        );
    }

    public function refund(string $transactionId, float $amount): bool
    {
        $response = Http::withHeaders([
            'Authorization' => 'Key ' . $this->secretKey,
        ])->post($this->baseUrl . '/api/v2/refund/', [
            'pidx' => $transactionId,
        ]);

        if (! $response->successful()) {
            throw new PaymentFailedException(
                'Khalti refund failed: ' . $response->json('detail', $response->body())
            );
        }

        return true;
    }
}
