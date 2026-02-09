@extends('layouts.catalog')

@section('title', 'Konfirmasi Pembayaran')

@section('content')
<!-- Loading overlay: payment processing - user tidak bisa klik apapun sampai redirect atau modal ditutup -->
<div id="payment-loading-overlay" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-[100] flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 max-w-sm w-full mx-4 text-center shadow-2xl">
        <div class="inline-block w-12 h-12 border-4 border-emerald-custom border-t-transparent rounded-full animate-spin mb-4"></div>
        <p class="text-lg font-semibold text-black dark:text-white">Memproses pembayaran...</p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Selesaikan di jendela pembayaran. Jangan tutup halaman ini.</p>
    </div>
</div>

<div class="min-h-screen bg-white dark:bg-black py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-black dark:text-white mb-2">Konfirmasi Pembayaran</h1>
            <p class="text-gray-600 dark:text-gray-400">Order #{{ $order->order_number }}</p>
        </div>

        <!-- Order Summary -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 mb-6">
            <h2 class="text-xl font-bold text-black dark:text-white mb-4">Detail Pesanan</h2>
            <div class="space-y-4 mb-4">
                @foreach($order->items as $item)
                    <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <p class="font-medium text-black dark:text-white">{{ $item->product_name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Qty: {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        <span class="font-semibold text-black dark:text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="space-y-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                <div class="flex justify-between text-gray-600 dark:text-gray-400">
                    <span>Subtotal</span>
                    <span class="font-medium text-black dark:text-white">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-gray-600 dark:text-gray-400">
                    <span>Pengiriman ({{ strtoupper($order->shipping_courier) }} - {{ $order->shipping_service }})</span>
                    <span class="font-medium text-black dark:text-white">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold text-black dark:text-white pt-2">
                    <span>Total</span>
                    <span class="text-emerald-custom">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="mt-4 p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 text-sm">
                <p class="font-medium text-black dark:text-white mb-1">Alamat pengiriman</p>
                <p class="text-gray-600 dark:text-gray-400">{{ $order->shipping_address }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ $order->shipping_district ?? '' }}, {{ $order->shipping_city ?? '' }}, {{ $order->shipping_province ?? '' }} {{ $order->shipping_postal_code ? ' - ' . $order->shipping_postal_code : '' }}</p>
            </div>
            <p class="mt-3 text-sm text-amber-600 dark:text-amber-400">Order akan kedaluwarsa dalam 30 menit</p>
        </div>

        <!-- Pilih metode pembayaran (Midtrans Snap) -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 mb-6">
            <h2 class="text-xl font-bold text-black dark:text-white mb-4">Pilih Metode Pembayaran</h2>
            <div id="snap-container">
                <div id="midtrans-snap"></div>
            </div>
        </div>

        @if(!config('services.midtrans.is_production'))
        <details class="mb-6 rounded-xl border border-amber-200 dark:border-amber-800 bg-amber-50/50 dark:bg-amber-900/10">
            <summary class="cursor-pointer p-4 text-sm font-medium text-amber-800 dark:text-amber-200">Panduan testing (Sandbox)</summary>
            <div class="p-4 pt-0 text-xs text-amber-800 dark:text-amber-200 space-y-2">
                <p><strong>Kartu kredit (transaksi sukses):</strong> No. kartu <code class="bg-amber-100 dark:bg-amber-900/30 px-1 rounded">4811 1111 1111 1114</code> (VISA), CVV <code class="px-1 rounded bg-amber-100 dark:bg-amber-900/30">123</code>, Exp 01/2025, OTP <code class="px-1 rounded bg-amber-100 dark:bg-amber-900/30">112233</code>.</p>
                <p><strong>VA / QRIS / E-Wallet:</strong> Gunakan simulator di dashboard Sandbox Midtrans atau <a href="https://simulator.sandbox.midtrans.com" target="_blank" rel="noopener" class="underline">simulator.sandbox.midtrans.com</a>.</p>
                <p class="text-amber-700 dark:text-amber-300">Jangan bayar dengan uang asli ke nomor VA/QR Sandbox. Hanya untuk testing.</p>
            </div>
        </details>
        @endif

        <div class="mt-6 text-center">
            <a href="{{ route('payment.failed', $order->id) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-emerald-custom transition-colors">
                Batalkan Pembayaran
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ config('services.midtrans.is_production', false) ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    (function() {
        var overlay = document.getElementById('payment-loading-overlay');
        window.snap.pay('{{ $snapToken }}', {
            onSuccess: function(result) {
                if (overlay) overlay.classList.remove('hidden');
                window.location.href = '{{ route('payment.success', $order->id) }}';
            },
            onPending: function(result) {
                if (overlay) overlay.classList.remove('hidden');
                window.location.href = '{{ route('payment.success', $order->id) }}';
            },
            onError: function(result) {
                if (overlay) overlay.classList.remove('hidden');
                window.location.href = '{{ route('payment.failed', $order->id) }}';
            },
            onClose: function() {
                if (overlay) overlay.classList.add('hidden');
            }
        });
    })();
</script>
@endpush
@endsection

