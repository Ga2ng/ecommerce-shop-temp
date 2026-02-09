@extends('layouts.catalog')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900 py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-black dark:text-white mb-2">Order Page</h1>
            <p class="text-gray-600 dark:text-gray-400">Detail pesanan Anda</p>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 mb-6">
            <h2 class="text-xl font-bold text-black dark:text-white mb-4">Detail Pesanan</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Nomor Order</span>
                    <span class="font-semibold text-black dark:text-white font-mono">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Status</span>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold
                        @if($order->status === 'paid') bg-emerald-custom/10 text-emerald-custom
                        @elseif($order->status === 'pending_payment') bg-amber-500/10 text-amber-600 dark:text-amber-400
                        @elseif($order->status === 'expired') bg-gray-500/10 text-gray-600 dark:text-gray-400
                        @else bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 @endif">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Total</span>
                    <span class="font-bold text-emerald-custom">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Items -->
        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 mb-6">
            <h3 class="font-bold text-black dark:text-white mb-4">Item Pesanan</h3>
            <div class="space-y-3">
                @foreach($order->items as $item)
                    <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-800 last:border-0">
                        <div>
                            <p class="font-medium text-black dark:text-white">{{ $item->product_name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                        </div>
                        <span class="font-semibold text-black dark:text-white">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 space-y-1">
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>Subtotal</span>
                    <span class="font-medium text-black dark:text-white">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>Pengiriman ({{ strtoupper($order->shipping_courier ?? '-') }} {{ $order->shipping_service ?? '' }})</span>
                    <span class="font-medium text-black dark:text-white">Rp {{ number_format($order->shipping_cost ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold text-black dark:text-white pt-2">
                    <span>Total</span>
                    <span class="text-emerald-custom">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="mt-4 p-3 rounded-lg bg-gray-50 dark:bg-gray-800/50 text-sm">
                <p class="font-medium text-black dark:text-white mb-1">Alamat pengiriman</p>
                <p class="text-gray-600 dark:text-gray-400">{{ $order->shipping_address }}</p>
                <p class="text-gray-600 dark:text-gray-400">{{ $order->shipping_district ?? '' }}, {{ $order->shipping_city ?? '' }}, {{ $order->shipping_province ?? '' }}{{ $order->shipping_postal_code ? ' ' . $order->shipping_postal_code : '' }}</p>
            </div>
        </div>

        @if($order->status === 'pending_payment' && !$order->isExpired())
            <div class="text-center mb-6">
                <a href="{{ route('payment.payment', $order->id) }}" class="inline-block px-6 py-3 bg-emerald-custom text-white font-semibold rounded-xl hover:bg-[#0ea572] transition-colors">
                    Lanjutkan Pembayaran
                </a>
            </div>
        @endif

        @if($order->status === 'paid')
            <div class="text-center mb-6">
                <a href="{{ route('order.receipt', $order->order_number) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-800 dark:bg-gray-700 text-white font-semibold rounded-xl hover:bg-gray-900 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download struk
                </a>
            </div>
        @endif

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('catalog.index') }}" class="px-6 py-3 bg-emerald-custom text-white font-semibold rounded-xl hover:bg-[#0ea572] transition-colors text-center">
                Lanjut Belanja
            </a>
            <a href="{{ route('home') }}" class="px-6 py-3 border-2 border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:border-emerald-custom hover:text-emerald-custom transition-colors text-center">
                Cek Order Lain
            </a>
        </div>
    </div>
</div>
@endsection
