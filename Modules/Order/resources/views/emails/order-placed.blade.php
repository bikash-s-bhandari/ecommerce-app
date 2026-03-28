<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: #1a1a2e; color: #fff; padding: 30px 40px; }
        .header h1 { margin: 0; font-size: 22px; }
        .header p { margin: 4px 0 0; color: #aaa; font-size: 14px; }
        .body { padding: 30px 40px; }
        .body p { color: #444; line-height: 1.6; }
        .order-meta { background: #f9f9f9; border-radius: 6px; padding: 16px 20px; margin: 20px 0; }
        .order-meta span { display: block; color: #888; font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        .order-meta strong { font-size: 16px; color: #1a1a2e; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th { background: #f0f0f0; text-align: left; padding: 10px 12px; font-size: 13px; color: #555; }
        td { padding: 10px 12px; border-bottom: 1px solid #f0f0f0; font-size: 14px; color: #333; }
        .totals td { border: none; }
        .totals .label { color: #888; }
        .totals .grand-total td { font-weight: bold; font-size: 15px; color: #1a1a2e; border-top: 2px solid #eee; padding-top: 14px; }
        .footer { background: #f9f9f9; padding: 20px 40px; text-align: center; color: #aaa; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Order Confirmed!</h1>
        <p>Thank you for your purchase, {{ $order->user->name }}.</p>
    </div>
    <div class="body">
        <div class="order-meta">
            <span>Order Number</span>
            <strong>{{ $order->order_number }}</strong>
        </div>

        <p>We've received your order and it's being processed. You'll receive another email once it ships.</p>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product_title }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rs. {{ number_format($item->unit_price, 2) }}</td>
                    <td>Rs. {{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals">
            <tr>
                <td class="label">Subtotal</td>
                <td align="right">Rs. {{ number_format($order->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Tax (10%)</td>
                <td align="right">Rs. {{ number_format($order->tax, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td><strong>Total</strong></td>
                <td align="right"><strong>Rs. {{ number_format($order->total, 2) }}</strong></td>
            </tr>
        </table>

        <p><strong>Shipping to:</strong><br>
            {{ $order->shipping_address['name'] ?? '' }}<br>
            {{ $order->shipping_address['address'] ?? '' }},
            {{ $order->shipping_address['city'] ?? '' }}<br>
            {{ $order->shipping_address['phone'] ?? '' }}
        </p>
    </div>
    <div class="footer">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</div>
</body>
</html>
