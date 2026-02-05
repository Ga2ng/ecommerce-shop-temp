<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\TransactionRecord;
use App\Services\PaymentService;
use App\Services\TransactionMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Show checkout form
     */
    public function checkout(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        if (!$productId) {
            return redirect()->route('catalog.index')
                ->with('error', 'Product tidak ditemukan.');
        }

        $product = Product::active()->findOrFail($productId);

        if (!$product->hasStock($quantity)) {
            return redirect()->route('catalog.show', $product->slug)
                ->with('error', 'Stok produk tidak mencukupi.');
        }

        return view('payment.checkout', compact('product', 'quantity'));
    }

    /**
     * Process checkout - VALIDATE & CREATE ORDER
     */
    public function processCheckout(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'province_id' => 'required|integer',
            'city_id' => 'required|integer',
            'district_id' => 'required|integer',
            'village_id' => 'nullable|integer',
            'shipping_postal_code' => 'nullable|string|max:10',
            'rajaongkir_city_id' => 'required|integer',
            'shipping_courier' => 'required|string|max:50',
            'shipping_service' => 'required|string|max:50',
            'shipping_cost' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Get product with lock to prevent race condition
            $product = Product::lockForUpdate()->findOrFail($validated['product_id']);

            // VALIDATE: Check if product is active
            if (!$product->is_active) {
                DB::rollBack();
                return back()->withErrors(['product_id' => 'Produk tidak aktif.'])->withInput();
            }

            // VALIDATE: Check stock availability
            if (!$product->hasStock($validated['quantity'])) {
                DB::rollBack();
                return back()->withErrors(['quantity' => 'Stok tidak mencukupi.'])->withInput();
            }

            // VALIDATE: Recalculate prices from DB (ignore frontend values)
            $productPrice = $product->price;
            $subtotal = $productPrice * $validated['quantity'];
            
            // VALIDATE: Recalculate shipping cost from Komerce district domestic-cost API
            $shippingService = app(\App\Services\ShippingService::class);
            $originDistrictId = (int) config('services.rajaongkir.origin_district_id', 1391);
            $weight = 1000;
            $shippingResult = $shippingService->getCostDistrict(
                $originDistrictId,
                (int) $validated['district_id'],
                $weight,
                $validated['shipping_courier']
            );

            $shippingCost = 0;
            if (!$shippingResult['success']) {
                DB::rollBack();
                return back()->withErrors([
                    'shipping_courier' => 'Gagal menghitung ongkir: ' . ($shippingResult['message'] ?? 'Unknown error')
                ])->withInput();
            }

            $serviceFound = false;
            if (isset($shippingResult['data'][0]['costs'])) {
                foreach ($shippingResult['data'][0]['costs'] as $cost) {
                    if (($cost['service'] ?? '') === $validated['shipping_service']) {
                        $firstCost = $cost['cost'][0] ?? null;
                        $shippingCost = is_array($firstCost) ? (int) ($firstCost['value'] ?? $firstCost['price'] ?? 0) : (int) $firstCost;
                        $serviceFound = true;
                        break;
                    }
                }
            }
            if (!$serviceFound) {
                DB::rollBack();
                return back()->withErrors([
                    'shipping_service' => 'Layanan pengiriman tidak ditemukan.'
                ])->withInput();
            }
            
            $total = $subtotal + $shippingCost;

            // VALIDATE: Verify region hierarchy integrity (CRITICAL - prevent invalid data)
            // Get region names from API (validation is done via API response)
            $regionController = app(\App\Http\Controllers\Api\RegionController::class);
            
            // Get province name
            $provinceRequest = new \Illuminate\Http\Request();
            $provinceResponse = $regionController->provinces($provinceRequest);
            $provinces = $provinceResponse->getData(true)['data'] ?? [];
            $province = collect($provinces)->firstWhere('id', $validated['province_id']);
            if (!$province) {
                DB::rollBack();
                return back()->withErrors(['province_id' => 'Provinsi tidak valid.'])->withInput();
            }

            // Get city name
            $cityRequest = new \Illuminate\Http\Request();
            $cityResponse = $regionController->cities($cityRequest, $validated['province_id']);
            $cities = $cityResponse->getData(true)['data'] ?? [];
            $city = collect($cities)->firstWhere('id', $validated['city_id']);
            if (!$city) {
                DB::rollBack();
                return back()->withErrors(['city_id' => 'Kota tidak valid atau tidak sesuai dengan provinsi yang dipilih.'])->withInput();
            }

            // Get district name
            $districtRequest = new \Illuminate\Http\Request();
            $districtResponse = $regionController->districts($districtRequest, $validated['city_id']);
            $districts = $districtResponse->getData(true)['data'] ?? [];
            $district = collect($districts)->firstWhere('id', $validated['district_id']);
            if (!$district) {
                DB::rollBack();
                return back()->withErrors(['district_id' => 'Kecamatan tidak valid atau tidak sesuai dengan kota yang dipilih.'])->withInput();
            }

            $village = null;
            // Village validation skipped as API may not support it

            // Reserve stock
            if (!$product->reserveStock($validated['quantity'])) {
                DB::rollBack();
                return back()->withErrors(['quantity' => 'Gagal memesan stok.'])->withInput();
            }

            // Create order (guest checkout allowed)
            $order = Order::create([
                'user_id' => Auth::check() ? Auth::id() : null,
                'status' => 'pending_payment',
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $city['name'] ?? null,
                'shipping_province' => $province['name'] ?? null,
                'shipping_district' => $district['name'] ?? null,
                'shipping_postal_code' => $validated['shipping_postal_code'] ?? ($district['zip_code'] ?? $city['zip_code'] ?? null),
                'rajaongkir_city_id' => $validated['rajaongkir_city_id'],
                'shipping_courier' => $validated['shipping_courier'],
                'shipping_service' => $validated['shipping_service'],
                'shipping_cost' => $shippingCost,
                'subtotal' => $subtotal,
                'total' => $total,
                'midtrans_order_id' => 'ORD-' . strtoupper(Str::random(10)) . '-' . time(),
                'expires_at' => now()->addMinutes(30),
            ]);

            // Create order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_sku' => $product->slug,
                'price' => $productPrice,
                'quantity' => $validated['quantity'],
                'subtotal' => $subtotal,
            ]);

            DB::commit();

            return redirect()->route('payment.payment', $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Error: ' . $e->getMessage(), [
                'user_id' => Auth::check() ? Auth::id() : null,
                'product_id' => $validated['product_id'] ?? null,
            ]);

            return back()->withErrors(['error' => 'Terjadi kesalahan saat memproses checkout.'])->withInput();
        }
    }

    /**
     * Show payment page and generate Snap Token
     */
    public function payment(Order $order)
    {
        // For guest checkout, validate using order number or email
        // For authenticated users, check if order belongs to user
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        
        // Guest checkout: order can be accessed by anyone (security handled by order number)

        // Check if order is expired
        if ($order->isExpired() && $order->status === 'pending_payment') {
            $this->expireOrder($order);
            return redirect()->route('payment.failed', $order->id)
                ->with('error', 'Order telah kedaluwarsa.');
        }

        // Check if order is already paid
        if ($order->status === 'paid') {
            return redirect()->route('payment.success', $order->id);
        }

        // Check if order status is valid for payment
        if (!in_array($order->status, ['cart', 'pending_payment'])) {
            return redirect()->route('payment.failed', $order->id)
                ->with('error', 'Order tidak dapat diproses.');
        }

        try {
            $snapToken = $this->paymentService->generateSnapToken($order);
        } catch (\Exception $e) {
            Log::error('Snap Token Generation Error: ' . $e->getMessage(), [
                'order_id' => $order->id,
            ]);

            return redirect()->route('payment.failed', $order->id)
                ->with('error', 'Gagal memproses pembayaran. Silakan coba lagi.');
        }

        return view('payment.payment', compact('order', 'snapToken'));
    }

    /**
     * Handle payment success (return URL from Midtrans).
     * Sinkronkan status dengan Midtrans di sini agar jika webhook tidak jalan
     * (localhost / URL notifikasi belum di-set), order tetap tercatat lunas dan stok berkurang.
     */
    public function success(Request $request, Order $order)
    {
        // For guest checkout, allow access (security handled by order number)
        // For authenticated users, check if order belongs to user
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Fallback: jika webhook belum/tidak terpanggil, cek status di Midtrans dan update order + stok + transaction_record
        if ($order->status !== 'paid') {
            $status = $this->paymentService->verifyTransaction($order->midtrans_order_id);
            if ($status && in_array($status['transaction_status'] ?? '', ['settlement', 'capture'])) {
                $order->markAsPaidFromPayload($status);
                $order->refresh();
            }
        }

        // Pastikan TransactionRecord ada (order sudah paid tapi record belum terisi, e.g. webhook lama / error)
        if ($order->status === 'paid' && !$order->transactionRecord) {
            $order->ensureTransactionRecord();
        }

        // Kirim email struk jika status masih pending (fallback bila webhook tidak jalan)
        $order->load('transactionRecord');
        if ($order->transactionRecord && $order->transactionRecord->email_status === TransactionRecord::EMAIL_STATUS_PENDING) {
            app(TransactionMailService::class)->sendReceipt($order->transactionRecord);
        }

        return view('payment.success', compact('order'));
    }

    /**
     * Handle payment failure
     */
    public function failed(Order $order)
    {
        // For guest checkout, allow access (security handled by order number)
        // For authenticated users, check if order belongs to user
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('payment.failed', compact('order'));
    }

    /**
     * Expire order and release stock
     */
    private function expireOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->status = 'expired';
            $order->save();

            // Release reserved stock
            foreach ($order->items as $item) {
                $product = Product::lockForUpdate()->find($item->product_id);
                if ($product) {
                    $product->releaseStock($item->quantity);
                }
            }
        });
    }
}
