<?php

return [
    'name' => 'Payment',

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    | Change PAYMENT_GATEWAY in .env to swap the active gateway.
    | No code change needed anywhere else.
    |
    | Supported: 'stripe', 'esewa', 'khalti'
    */
    'default' => env('PAYMENT_GATEWAY', 'stripe'),

    'gateways' => [

        'stripe' => [
            'secret'         => env('STRIPE_SECRET'),
            'public_key'     => env('STRIPE_PUBLIC_KEY'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        ],

        /*
        | eSewa Sandbox (test) credentials:
        |   merchant_code : EPAYTEST
        |   secret_key    : 8gBm/:&EnhH.1/q
        |   base_url      : https://rc-epay.esewa.com.np/api/epay/main/v2/form
        |   Test IDs      : 9806800001 – 9806800005  (MPIN: 1122, Token: 123456)
        |
        | eSewa Production:
        |   base_url      : https://epay.esewa.com.np/api/epay/main/v2/form
        */
        'esewa' => [
            'merchant_code' => env('ESEWA_MERCHANT_CODE', 'EPAYTEST'),
            'secret_key'    => env('ESEWA_SECRET_KEY', '8gBm/:&EnhH.1/q'),
            'base_url'      => env('ESEWA_BASE_URL', 'https://rc-epay.esewa.com.np/api/epay/main/v2/form'),
        ],

        /*
        | Khalti Sandbox credentials:
        |   base_url   : https://a.khalti.com
        |   secret_key : get from https://test-admin.khalti.com
        |   Test phone : 9800000001 – 9800000005  (MPIN: 1111, OTP: 987654)
        |
        | Khalti Production:
        |   base_url   : https://khalti.com  (same API path)
        */
        'khalti' => [
            'secret_key' => env('KHALTI_SECRET_KEY'),
            'base_url'   => env('KHALTI_BASE_URL', 'https://a.khalti.com'),
        ],

    ],
];
