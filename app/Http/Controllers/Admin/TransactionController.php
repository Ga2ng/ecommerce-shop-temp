<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransactionRecord;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * Daftar semua transaksi berhasil (untuk analisis dashboard).
     */
    public function index(Request $request): View
    {
        $query = TransactionRecord::query()->with('order')->latest('paid_at');

        // Pencarian: no. order, nama, email
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('order_number', 'like', "%{$term}%")
                    ->orWhere('customer_name', 'like', "%{$term}%")
                    ->orWhere('customer_email', 'like', "%{$term}%");
            });
        }

        // Filter tanggal: dari
        if ($request->filled('date_from')) {
            $query->whereDate('paid_at', '>=', $request->date_from);
        }

        // Filter tanggal: sampai
        if ($request->filled('date_to')) {
            $query->whereDate('paid_at', '<=', $request->date_to);
        }

        // Filter metode pembayaran
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        $transactions = $query->paginate(20)->withQueryString();

        // Ringkasan (berdasarkan filter yang sama)
        $summaryQuery = TransactionRecord::query();
        if ($request->filled('search')) {
            $term = $request->search;
            $summaryQuery->where(function ($q) use ($term) {
                $q->where('order_number', 'like', "%{$term}%")
                    ->orWhere('customer_name', 'like', "%{$term}%")
                    ->orWhere('customer_email', 'like', "%{$term}%");
            });
        }
        if ($request->filled('date_from')) {
            $summaryQuery->whereDate('paid_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $summaryQuery->whereDate('paid_at', '<=', $request->date_to);
        }
        if ($request->filled('payment_type')) {
            $summaryQuery->where('payment_type', $request->payment_type);
        }

        $summaryFiltered = [
            'count' => $summaryQuery->count(),
            'total_revenue' => $summaryQuery->sum('total'),
        ];

        // Ringkasan global (tanpa filter) untuk kartu
        $statsToday = TransactionRecord::whereDate('paid_at', today())->selectRaw('count(*) as cnt, coalesce(sum(total), 0) as rev')->first();
        $statsThisWeek = TransactionRecord::whereBetween('paid_at', [now()->startOfWeek(), now()->endOfWeek()])->selectRaw('count(*) as cnt, coalesce(sum(total), 0) as rev')->first();
        $statsAllTime = TransactionRecord::selectRaw('count(*) as cnt, coalesce(sum(total), 0) as rev')->first();

        $paymentTypes = TransactionRecord::whereNotNull('payment_type')
            ->distinct()
            ->pluck('payment_type')
            ->sort()
            ->values();

        return view('admin.transactions.index', [
            'transactions' => $transactions,
            'summaryFiltered' => $summaryFiltered,
            'statsToday' => $statsToday,
            'statsThisWeek' => $statsThisWeek,
            'statsAllTime' => $statsAllTime,
            'paymentTypes' => $paymentTypes,
            'filters' => $request->only(['search', 'date_from', 'date_to', 'payment_type']),
        ]);
    }
}
