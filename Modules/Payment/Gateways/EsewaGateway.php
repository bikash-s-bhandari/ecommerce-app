<?php

namespace Modules\Payment\Gateways;

use Modules\Payment\Contracts\PaymentGatewayInterface;
use Modules\Payment\DTOs\PaymentInitiateDTO;
use Modules\Payment\DTOs\PaymentInitiateResultDTO;
use Modules\Payment\DTOs\PaymentVerifyDTO;
use Modules\Payment\DTOs\PaymentVerifyResultDTO;
use Modules\Payment\Enums\PaymentStatusEnum;
use Modules\Payment\Exceptions\PaymentFailedException;

/**
 * eSewa V2 Payment Gateway (Nepal)
 *
 * Flow:
 *   1. initiate()  → builds HMAC-SHA256 signed form fields
 *                    Frontend auto-submits an HTML form to eSewa's URL
 *                    (type = 'form', redirectUrl = eSewa endpoint)
 *
 *   2. eSewa redirects to success_url with ?data=<base64-encoded-JSON>
 *
 *   3. verify()    → decodes the base64 data, verifies HMAC signature,
 *                    returns PaymentVerifyResultDTO with PAID / FAILED
 *
 * Test credentials (sandbox):
 *   merchant_code : EPAYTEST
 *   secret_key    : 8gBm/:&EnhH.1/q
 *   base_url      : https://rc-epay.esewa.com.np/api/epay/main/v2/form
 *   test eSewa ID : 9806800001 ... 9806800005
 *   MPIN          : 1122  |  Token: 123456
 *
 * LSP compliance:
 *   - initiate() always returns PaymentInitiateResultDTO (type='form')
 *   - verify()   always returns PaymentVerifyResultDTO
 *   - errors     always throw PaymentFailedException
 */
class EsewaGateway implements PaymentGatewayInterface
{
    private string $merchantCode;
    private string $secretKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->merchantCode = config('payment.gateways.esewa.merchant_code');
        $this->secretKey    = config('payment.gateways.esewa.secret_key');
        $this->baseUrl      = config('payment.gateways.esewa.base_url');
    }

    public function initiate(PaymentInitiateDTO $dto): PaymentInitiateResultDTO
    {
        // Unique transaction ID – used later during verify to match the order
        $transactionUuid = 'ORD-' . $dto->orderId . '-' . uniqid();

        $totalAmount = number_format($dto->amount, 2, '.', '');

        // eSewa V2 requires these exact fields in the HMAC message
        $signedFieldNames = 'total_amount,transaction_uuid,product_code';
        $message          = "total_amount={$totalAmount},transaction_uuid={$transactionUuid},product_code={$this->merchantCode}";
        $signature        = base64_encode(hash_hmac('sha256', $message, $this->secretKey, true));

        $formData = [
            'amount'                   => $totalAmount,
            'tax_amount'               => '0',
            'total_amount'             => $totalAmount,
            'transaction_uuid'         => $transactionUuid,
            'product_code'             => $this->merchantCode,
            'product_service_charge'   => '0',
            'product_delivery_charge'  => '0',
            'success_url'              => $dto->returnUrl . '?gateway=esewa',
            'failure_url'              => $dto->returnUrl . '?gateway=esewa&status=failure',
            'signed_field_names'       => $signedFieldNames,
            'signature'                => $signature,
        ];

        return new PaymentInitiateResultDTO(
            type:         'form',
            gatewayRef:   $transactionUuid,
            redirectUrl:  $this->baseUrl,
            clientSecret: null,
            formData:     $formData,
            raw:          $formData,
        );
    }

    public function verify(PaymentVerifyDTO $dto): PaymentVerifyResultDTO
    {
        // eSewa sends ?data=<base64-encoded-JSON> on the success_url
        $encoded = $dto->callbackData['data'] ?? null;

        if (! $encoded) {
            throw new PaymentFailedException('eSewa callback missing data parameter.');
        }

        $decoded = json_decode(base64_decode($encoded), true);

        if (! $decoded || ! isset($decoded['signed_field_names'], $decoded['signature'])) {
            throw new PaymentFailedException('eSewa callback data is malformed.');
        }

        // Rebuild HMAC from the exact fields eSewa signed
        $signedFields = explode(',', $decoded['signed_field_names']);
        $message      = collect($signedFields)
            ->map(fn ($field) => "{$field}={$decoded[$field]}")
            ->implode(',');

        $expectedSignature = base64_encode(hash_hmac('sha256', $message, $this->secretKey, true));

        if (! hash_equals($expectedSignature, $decoded['signature'])) {
            throw new PaymentFailedException('eSewa signature verification failed. Possible tampering.');
        }

        $status = ($decoded['status'] ?? '') === 'COMPLETE'
            ? PaymentStatusEnum::PAID
            : PaymentStatusEnum::FAILED;

        return new PaymentVerifyResultDTO(
            status:     $status,
            gatewayRef: $decoded['transaction_uuid'],
            amount:     (float) ($decoded['total_amount'] ?? 0),
            currency:   'NPR',
            raw:        $decoded,
        );
    }

    public function refund(string $transactionId, float $amount): bool
    {
        // eSewa does not provide a programmatic refund API.
        // Refunds must be initiated through eSewa's merchant portal.
        throw new PaymentFailedException(
            'eSewa automated refund is not supported. Please initiate refund through eSewa merchant portal.'
        );
    }
}
