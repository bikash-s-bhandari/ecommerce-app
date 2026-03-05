<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ $subject ?? 'Order Update' }}</title>
</head>

<body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding:
20px;">
    <h2 style="color: #1E3A5F;">{{ $greeting }}</h2>
    <p>{{ $body }}</p>
    <div style="margin: 30px 0; background: #f8f9fa; padding: 20px; border-radius: 8px;">
        <h3>Order Summary — #{{ $order->order_number }}</h3>
        @foreach ($order->items as $item)
            <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                <span>{{ $item->product_title }} x{{ $item->quantity }}</span>
                <span>${{ number_format($item->subtotal, 2) }}</span>
            </div>
        @endforeach
        <hr>
        <strong>Total: ${{ number_format($order->total, 2) }}</strong>
    </div>
    <a href="{{ $actionUrl }}"
        style="background: #1E3A5F; color: white; padding: 12px 24px; text-decoration: none;
border-radius: 4px;">
        {{ $actionText }}
    </a>
    <p style="color: #999; margin-top: 40px; font-size: 12px;">
        &copy; {{ date('Y') }} ECommerce. All rights reserved.
    </p>
</body>

</html>
