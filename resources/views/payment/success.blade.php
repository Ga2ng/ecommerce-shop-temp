@extends('layouts.catalog')

@section('title', 'Payment Success')

@section('content')
<div class="min-h-screen bg-white dark:bg-black py-12">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-black dark:text-white mb-2">Payment Received</h1>
            <p class="text-gray-600 dark:text-gray-400">Terima kasih atas pembayaran Anda</p>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 mb-6">
            <h2 class="text-xl font-bold text-black dark:text-white mb-4">Order Details</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Order Number</span>
                    <span class="font-semibold text-black dark:text-white">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Status</span>
                    <span class="px-3 py-1 bg-emerald-custom/10 text-emerald-custom rounded-full text-sm font-semibold">
                        {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Total Amount</span>
                    <span class="font-bold text-emerald-custom">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-6 mb-6">
            <h3 class="font-semibold text-black dark:text-white mb-2">What's Next?</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Kami akan memproses pesanan Anda dan mengirimkan konfirmasi melalui email. 
                Status pembayaran akan diperbarui setelah konfirmasi dari payment gateway.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('catalog.index') }}" class="px-6 py-3 bg-emerald-custom text-white font-semibold rounded-xl hover:bg-[#0ea572] transition-colors text-center">
                Continue Shopping
            </a>
            <a href="{{ route('dashboard') }}" class="px-6 py-3 border-2 border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:border-emerald-custom hover:text-emerald-custom transition-colors text-center">
                View Orders
            </a>
        </div>
    </div>
</div>
@endsection

