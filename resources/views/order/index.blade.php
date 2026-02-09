@extends('layouts.catalog')

@section('title', 'Cari Nomor Order')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900 py-12">
    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-custom/10 text-emerald-custom mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-black dark:text-white mb-2">Cari nomor order</h1>
            <p class="text-gray-600 dark:text-gray-400">Masukkan nomor order Anda untuk melihat detail pesanan dan download struk.</p>
        </div>

        <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 lg:p-8 shadow-sm">
            <form action="{{ route('order.lookup') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="order_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nomor order</label>
                    <input type="text"
                           name="order_number"
                           id="order_number"
                           value="{{ old('order_number') }}"
                           placeholder="Contoh: ORD-XXXXXXXX-20260209"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-black dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-emerald-custom focus:border-transparent"
                           required
                           autofocus
                    >
                    @if(session('order_lookup_error'))
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ session('order_lookup_error') }}</p>
                    @endif
                </div>
                <button type="submit" class="w-full px-6 py-3 bg-emerald-custom text-white font-semibold rounded-lg hover:bg-[#0ea572] transition-colors">
                    Lihat order
                </button>
            </form>
        </div>

        <p class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-emerald-custom transition-colors">← Kembali ke beranda</a>
        </p>
    </div>
</div>
@endsection
