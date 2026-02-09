<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Struk {{ $order->order_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #10B981; padding-bottom: 12px; }
        .header h1 { margin: 0; font-size: 18px; color: #10B981; }
        .header p { margin: 4px 0 0; font-size: 11px; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th, td { padding: 8px 6px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { font-size: 10px; text-transform: uppercase; color: #6b7280; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; font-size: 13px; border-top: 2px solid #10B981; }
        .footer { margin-top: 24px; font-size: 10px; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name', 'E-comm') }}</h1>
        <p>Struk Pembayaran</p>
    </div>

    <p><strong>No. Order:</strong> {{ $order->order_number }}</p>
    <p><strong>Tanggal:</strong> {{ $order->paid_at ? $order->paid_at->translatedFormat('d F Y H:i') : $order->created_at->translatedFormat('d F Y H:i') }}</p>
    <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $order->status)) }}</p>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Harga</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <tr>
            <td>Subtotal</td>
            <td class="text-right">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Pengiriman ({{ strtoupper($order->shipping_courier ?? '-') }} {{ $order->shipping_service ?? '' }})</td>
            <td class="text-right">Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td>Total</td>
            <td class="text-right">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <p><strong>Penerima:</strong><br>{{ $order->customer_name }}<br>{{ $order->customer_email }}<br>{{ $order->customer_phone }}</p>
    <p><strong>Alamat pengiriman:</strong><br>{{ $order->shipping_address }}, {{ $order->shipping_district ?? '' }}, {{ $order->shipping_city ?? '' }}, {{ $order->shipping_province ?? '' }} {{ $order->shipping_postal_code ?? '' }}</p>

    <div class="footer">
        <p>Terima kasih telah berbelanja di {{ config('app.name', 'E-comm') }}</p>
    </div>
</body>
</html>
