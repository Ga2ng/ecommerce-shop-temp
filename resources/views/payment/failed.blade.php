@extends('layouts.catalog')

@section('title', 'Payment Failed')

@section('content')
<div class="min-h-screen bg-white dark:bg-black py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-black dark:text-white mb-2">Payment Failed</h1>
            <p class="text-gray-600 dark:text-gray-400">Pembayaran tidak dapat diproses</p>
        </div>

        @if(session('error'))
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-6 mb-6">
                <p class="text-red-800 dark:text-red-300">{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 mb-6">
            <h2 class="text-xl font-bold text-black dark:text-white mb-4">Order Details</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Order Number</span>
                    <span class="font-semibold text-black dark:text-white">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Status</span>
                    <span class="px-3 py-1 bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-full text-sm font-semibold">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Total Amount</span>
                    <span class="font-bold text-black dark:text-white">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-6 mb-6">
            <h3 class="font-semibold text-black dark:text-white mb-2">What Happened?</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Pembayaran Anda tidak dapat diproses. Ini bisa terjadi karena:
            </p>
            <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-2 list-disc list-inside">
                <li>Pembayaran dibatalkan atau kedaluwarsa</li>
                <li>Masalah dengan metode pembayaran yang dipilih</li>
                <li>Order telah kedaluwarsa (lebih dari 30 menit)</li>
            </ul>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @if($order->status === 'pending_payment' && !$order->isExpired())
                <a href="{{ route('payment.payment', $order->id) }}" class="px-6 py-3 bg-emerald-custom text-white font-semibold rounded-xl hover:bg-[#0ea572] transition-colors text-center">
                    Try Again
                </a>
            @endif
            <a href="{{ route('catalog.index') }}" class="px-6 py-3 border-2 border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:border-emerald-custom hover:text-emerald-custom transition-colors text-center">
                Continue Shopping
            </a>
        </div>
    </div>
</div>
@endsection

