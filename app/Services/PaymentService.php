<?php

namespace App\Services;

use App\Models\Order;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use Illuminate\Support\Facades\Log;

/**
 * Midtrans integration.
 * Backend: Server Key dipakai sebagai Basic Auth (base64(ServerKey:)) ke Snap API.
 * Frontend: Client Key dipakai di snap.js, jangan pakai Server Key di frontend.
 * Sandbox: https://app.sandbox.midtrans.com/snap/v1/transactions
 * Production: https://app.midtrans.com/snap/v1/transactions
 */
class PaymentService
{
    public function __construct()
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production', false);
        Config::$isSanitized = config('services.midtrans.is_sanitized', true);
        Config::$is3ds = config('services.midtrans.is_3ds', true);
    }

    /**
     * Generate Snap Token for order (POST ke Midtrans Snap API dengan Authorization: Basic base64(ServerKey:))
     */
    public function generateSnapToken(Order $order): string
    {
        $serverKey = config('services.midtrans.server_key');
        if (empty($serverKey)) {
            throw new \InvalidArgumentException('MIDTRANS_SERVER_KEY belum di-set di .env. Ambil dari Midtrans Dashboard → Settings → Access Keys.');
        }

        $params = [
            'transaction_details' => [
                'order_id' => $order->midtrans_order_id,
                'gross_amount' => (int) $order->total,
            ],
            'item_details' => $this->buildItemDetails($order),
            'customer_details' => [
                'first_name' => $order->customer_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
                'billing_address' => [
                    'first_name' => $order->customer_name,
                    'phone' => $order->customer_phone,
                    'address' => $order->shipping_address,
                    'city' => $order->shipping_city,
                    'postal_code' => $order->shipping_postal_code,
                    'country_code' => 'IDN',
                ],
                'shipping_address' => [
                    'first_name' => $order->customer_name,
                    'phone' => $order->customer_phone,
                    'address' => $order->shipping_address,
                    'city' => $order->shipping_city,
                    'postal_code' => $order->shipping_postal_code,
                    'country_code' => 'IDN',
                ],
            ],
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit' => 'minute',
                'duration' => 30, // 30 minutes
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token Error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'midtrans_order_id' => $order->midtrans_order_id,
            ]);
            throw $e;
        }
    }

    /**
     * Build item details for Midtrans
     */
    private function buildItemDetails(Order $order): array
    {
        $items = [];

        // Add product items
        foreach ($order->items as $item) {
            $items[] = [
                'id' => (string) $item->product_id,
                'price' => (int) $item->price,
                'quantity' => $item->quantity,
                'name' => $item->product_name,
            ];
        }

        // Add shipping cost as item (synchronized with RajaOngkir)
        if ($order->shipping_cost > 0) {
            $shippingName = 'Ongkos Kirim';
            if ($order->shipping_courier && $order->shipping_service) {
                $courierName = strtoupper($order->shipping_courier);
                $shippingName = "Ongkos Kirim {$courierName} - {$order->shipping_service}";
            } elseif ($order->shipping_courier) {
                $shippingName = 'Ongkos Kirim ' . strtoupper($order->shipping_courier);
            }
            
            $items[] = [
                'id' => 'shipping',
                'price' => (int) $order->shipping_cost,
                'quantity' => 1,
                'name' => $shippingName,
            ];
        }

        return $items;
    }

    /**
     * Verify transaction status from Midtrans.
     * SDK mengembalikan stdClass (json_decode tanpa true); dinormalisasi ke array agar konsisten.
     */
    public function verifyTransaction(string $orderId): ?array
    {
        try {
            $status = Transaction::status($orderId);
            return \is_array($status) ? $status : (array) $status;
        } catch (\Exception $e) {
            Log::error('Midtrans Status Check Error: ' . $e->getMessage(), [
                'order_id' => $orderId,
            ]);
            return null;
        }
    }

    /**
     * Verify signature key from webhook
     */
    public function verifySignature(array $data): bool
    {
        $orderId = $data['order_id'] ?? null;
        $statusCode = $data['status_code'] ?? null;
        $grossAmount = $data['gross_amount'] ?? null;
        $serverKey = config('services.midtrans.server_key');
        $signatureKey = $data['signature_key'] ?? null;

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey) {
            return false;
        }

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        return hash_equals($expectedSignature, $signatureKey);
    }
}

