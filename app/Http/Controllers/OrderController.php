<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Halaman index: cari nomor order (form dedicated).
     */
    public function index(): View
    {
        return view('order.index');
    }

    /**
     * Lookup order by order number (form dari home / cek order).
     * Redirect ke halaman order jika ditemukan.
     */
    public function lookup(Request $request): RedirectResponse
    {
        $request->validate([
            'order_number' => 'required|string|max:64',
        ], [
            'order_number.required' => 'Nomor order wajib diisi.',
        ]);

        $order = Order::where('order_number', trim($request->order_number))->first();

        if (!$order) {
            return redirect()->back()
                ->with('order_lookup_error', 'Order dengan nomor tersebut tidak ditemukan.')
                ->withInput($request->only('order_number'));
        }

        return redirect()->route('order.show', $order->order_number);
    }

    /**
     * Tampilkan halaman order (order page) by order number.
     * Bisa diakses guest dengan nomor order.
     */
    public function show(string $order_number): View|RedirectResponse
    {
        $order = Order::where('order_number', $order_number)->firstOrFail();

        // Auth: hanya pemilik order atau guest (dengan nomor order)
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('order.show', compact('order'));
    }

    /**
     * Download struk (PDF) by order number. Hanya untuk order yang sudah paid.
     */
    public function receiptPdf(string $order_number): Response
    {
        $order = Order::where('order_number', $order_number)->firstOrFail();

        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if ($order->status !== 'paid') {
            abort(404, 'Struk hanya tersedia untuk pesanan yang sudah dibayar.');
        }

        $pdf = Pdf::loadView('pdf.receipt', compact('order'));
        $filename = 'struk-' . $order->order_number . '.pdf';

        return $pdf->download($filename);
    }
}
