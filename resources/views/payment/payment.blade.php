@extends('layouts.catalog')

@section('title', 'Payment')

@section('content')
<div class="min-h-screen bg-white dark:bg-black py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-black dark:text-white mb-2">Payment</h1>
            <p class="text-gray-600 dark:text-gray-400">Order #{{ $order->order_number }}</p>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <span class="text-gray-600 dark:text-gray-400">Total Amount</span>
                <span class="text-2xl font-bold text-emerald-custom">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-500">Order akan kedaluwarsa dalam 30 menit</p>
        </div>

        <!-- Midtrans Snap Embed -->
        <div id="snap-container" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6">
            <div id="midtrans-snap"></div>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('payment.failed', $order->id) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-emerald-custom transition-colors">
                Cancel Payment
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ config('services.midtrans.is_production', false) ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    window.snap.pay('{{ $snapToken }}', {
        onSuccess: function(result) {
            window.location.href = '{{ route('payment.success', $order->id) }}';
        },
        onPending: function(result) {
            window.location.href = '{{ route('payment.success', $order->id) }}';
        },
        onError: function(result) {
            window.location.href = '{{ route('payment.failed', $order->id) }}';
        },
        onClose: function() {
            // User closed the popup without finishing the payment
        }
    });
</script>
@endpush
@endsection

