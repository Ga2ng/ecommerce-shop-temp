<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran – {{ $transaction->order_number }}</title>
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { border-bottom: 2px solid #10B981; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 1.25rem; color: #10B981; }
        .meta { font-size: 0.875rem; color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin: 16px 0; }
        th, td { padding: 8px 0; text-align: left; border-bottom: 1px solid #eee; }
        th { font-weight: 600; color: #555; }
        .text-right { text-align: right; }
        .total-row { font-size: 1.125rem; font-weight: 700; color: #10B981; border-bottom: none; padding-top: 12px; }
        .footer { margin-top: 24px; padding-top: 16px; border-top: 1px solid #eee; font-size: 0.8125rem; color: #888; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <p style="margin: 4px 0 0 0;">Struk Pembayaran</p>
    </div>

    <div class="meta">
        No. Order: <strong>{{ $transaction->order_number }}</strong><br>
        Tanggal: {{ $transaction->paid_at ? $transaction->paid_at->format('d M Y, H:i') : '–' }}<br>
        Metode: {{ $transaction->payment_method_label ?? $transaction->payment_type ?? '–' }}
    </div>

    <p>Halo <strong>{{ $transaction->customer_name }}</strong>,</p>
    <p>Pembayaran untuk pesanan berikut telah berhasil kami terima.</p>

    @php $order = $transaction->order; @endphp
    @if($order && $order->relationLoaded('items'))
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
    @endif

    <table>
        <tr>
            <td>Subtotal</td>
            <td class="text-right">Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Ongkos Kirim</td>
            <td class="text-right">Rp {{ number_format($transaction->shipping_cost ?? 0, 0, ',', '.') }}</td>
        </tr>
        <tr class="total-row">
            <td>Total Dibayar</td>
            <td class="text-right">Rp {{ number_format($transaction->total, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if($order)
        <p><strong>Alamat pengiriman:</strong><br>
        {{ $order->shipping_address }}, {{ $order->shipping_district ?? '' }} {{ $order->shipping_city ?? '' }}, {{ $order->shipping_province ?? '' }} {{ $order->shipping_postal_code ?? '' }}</p>
    @endif

    <div class="footer">
        Email ini dikirim otomatis. Jika ada pertanyaan, balas ke alamat pengirim.<br>
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </div>
</body>
</html>
