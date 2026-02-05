@extends('layouts.admin')

@section('title', 'Transaksi Berhasil')
@section('page-title', 'Transaksi Berhasil')

@section('content')
<div class="space-y-6">
    {{-- Kartu ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Hari ini</p>
            <p class="mt-1 text-2xl font-bold text-black dark:text-white">{{ $statsToday->cnt ?? 0 }} transaksi</p>
            <p class="text-sm font-semibold text-emerald-custom">Rp {{ number_format($statsToday->rev ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Minggu ini</p>
            <p class="mt-1 text-2xl font-bold text-black dark:text-white">{{ $statsThisWeek->cnt ?? 0 }} transaksi</p>
            <p class="text-sm font-semibold text-emerald-custom">Rp {{ number_format($statsThisWeek->rev ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-5">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total semua</p>
            <p class="mt-1 text-2xl font-bold text-black dark:text-white">{{ $statsAllTime->cnt ?? 0 }} transaksi</p>
            <p class="text-sm font-semibold text-emerald-custom">Rp {{ number_format($statsAllTime->rev ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4">
        <form method="get" action="{{ route('admin.transactions.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="min-w-[200px]">
                <label for="search" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Cari (no. order / nama / email)</label>
                <input type="text" name="search" id="search" value="{{ old('search', $filters['search'] ?? '') }}" placeholder="Cari..." class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-black dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-custom focus:border-emerald-custom">
            </div>
            <div>
                <label for="date_from" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Dari tanggal</label>
                <input type="date" name="date_from" id="date_from" value="{{ $filters['date_from'] ?? '' }}" class="rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-black dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-custom focus:border-emerald-custom">
            </div>
            <div>
                <label for="date_to" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Sampai tanggal</label>
                <input type="date" name="date_to" id="date_to" value="{{ $filters['date_to'] ?? '' }}" class="rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-black dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-custom focus:border-emerald-custom">
            </div>
            <div class="min-w-[160px]">
                <label for="payment_type" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Metode pembayaran</label>
                <select name="payment_type" id="payment_type" class="w-full rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-black dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-custom focus:border-emerald-custom">
                    <option value="">Semua</option>
                    @foreach($paymentTypes as $pt)
                        <option value="{{ $pt }}" {{ ($filters['payment_type'] ?? '') === $pt ? 'selected' : '' }}>{{ \App\Models\TransactionRecord::paymentTypeLabel($pt) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-emerald-custom text-white font-medium rounded-lg hover:bg-[#0ea572] transition-colors text-sm">Filter</button>
                <a href="{{ route('admin.transactions.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors text-sm">Reset</a>
            </div>
        </form>
        @if(request()->hasAny(['search', 'date_from', 'date_to', 'payment_type']))
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                Hasil filter: <strong>{{ $summaryFiltered['count'] }}</strong> transaksi, total revenue <strong class="text-emerald-custom">Rp {{ number_format($summaryFiltered['total_revenue'], 0, ',', '.') }}</strong>
            </p>
        @endif
    </div>

    {{-- Tabel transaksi --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800">
            <h3 class="text-lg font-semibold text-black dark:text-white">Daftar Transaksi Berhasil</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Hanya transaksi dengan status settlement/capture</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No. Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subtotal</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Ongkir</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pembayaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse($transactions as $tr)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-black dark:text-white">{{ $tr->order_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-black dark:text-white">{{ $tr->customer_name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $tr->customer_email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $tr->paid_at ? $tr->paid_at->format('d M Y, H:i') : '–' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700 dark:text-gray-300">
                                Rp {{ number_format($tr->subtotal, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-700 dark:text-gray-300">
                                Rp {{ number_format($tr->shipping_cost ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold text-emerald-custom">
                                Rp {{ number_format($tr->total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-full">
                                    {{ $tr->payment_method_label ?? $tr->payment_type ?? '–' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($tr->email_status === 'sent')
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300 rounded-full">Terkirim</span>
                                    @if($tr->email_sent_at)
                                        <span class="block text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $tr->email_sent_at->format('d/m H:i') }}</span>
                                    @endif
                                @else
                                    <span class="px-2 py-1 text-xs bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-300 rounded-full">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <form action="{{ route('admin.transactions.send-receipt', $tr) }}" method="post" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm text-emerald-custom hover:text-[#0ea572] font-medium">
                                        {{ $tr->email_status === 'sent' ? 'Kirim ulang' : 'Kirim email' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <p class="text-gray-500 dark:text-gray-400">Belum ada transaksi berhasil.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
