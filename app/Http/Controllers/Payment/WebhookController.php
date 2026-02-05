<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentLog;
use App\Models\Product;
use App\Models\TransactionRecord;
use App\Services\PaymentService;
use App\Services\TransactionMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Handle Midtrans webhook
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('Midtrans Webhook Received', $payload);

        // Validate signature
        if (!$this->paymentService->verifySignature($payload)) {
            Log::warning('Invalid Midtrans Signature', $payload);
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $paymentType = $payload['payment_type'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;

        if (!$orderId || !$transactionStatus) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        // Find order by midtrans_order_id
        $order = Order::where('midtrans_order_id', $orderId)->first();

        if (!$order) {
            Log::warning('Order not found for Midtrans webhook', ['order_id' => $orderId]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Check idempotency - prevent duplicate processing
        $existingLog = PaymentLog::where('midtrans_order_id', $orderId)
            ->where('transaction_status', $transactionStatus)
            ->where('is_valid', true)
            ->first();

        if ($existingLog) {
            Log::info('Duplicate webhook ignored', [
                'order_id' => $orderId,
                'status' => $transactionStatus,
            ]);
            return response()->json(['message' => 'Already processed'], 200);
        }

        // Save payment log
        PaymentLog::create([
            'order_id' => $order->id,
            'midtrans_order_id' => $orderId,
            'midtrans_transaction_id' => $payload['transaction_id'] ?? null,
            'transaction_status' => $transactionStatus,
            'payment_type' => $paymentType,
            'gross_amount' => $grossAmount,
            'request_payload' => json_encode($payload),
            'response_payload' => json_encode($payload),
            'signature_key' => $payload['signature_key'] ?? null,
            'is_valid' => true,
            'source' => 'webhook',
        ]);

        // Process transaction status
        try {
            DB::beginTransaction();

            switch ($transactionStatus) {
                case 'settlement':
                case 'capture':
                    $this->handleSettlement($order, $payload);
                    break;

                case 'pending':
                    $this->handlePending($order, $payload);
                    break;

                case 'expire':
                case 'cancel':
                case 'deny':
                    $this->handleFailure($order, $payload);
                    break;

                default:
                    Log::info('Unhandled transaction status', [
                        'order_id' => $orderId,
                        'status' => $transactionStatus,
                    ]);
            }

            DB::commit();

            // Kirim email struk otomatis setelah pembayaran berhasil
            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                $record = TransactionRecord::where('order_id', $order->id)->first();
                if ($record) {
                    app(TransactionMailService::class)->sendReceipt($record);
                }
            }

            return response()->json(['message' => 'OK'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Webhook Processing Error: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'payload' => $payload,
            ]);

            return response()->json(['message' => 'Processing error'], 500);
        }
    }

    /**
     * Handle settlement/capture (PAYMENT SUCCESS)
     */
    private function handleSettlement(Order $order, array $payload): void
    {
        $order->markAsPaidFromPayload($payload);

        Log::info('Order paid successfully', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ]);
    }

    /**
     * Handle pending payment
     */
    private function handlePending(Order $order, array $payload): void
    {
        if ($order->status !== 'pending_payment') {
            $order->status = 'pending_payment';
            $order->save();
        }

        $order->payment_status = 'pending';
        $order->midtrans_transaction_id = $payload['transaction_id'] ?? null;
        $order->midtrans_payment_type = $payload['payment_type'] ?? null;
        $order->save();

        Log::info('Order payment pending', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ]);
    }

    /**
     * Handle payment failure (expire, cancel, deny)
     */
    private function handleFailure(Order $order, array $payload): void
    {
        if (in_array($order->status, ['paid', 'shipped', 'completed'])) {
            return; // Don't change status if already paid
        }

        $order->status = 'expired';
        $order->payment_status = $payload['transaction_status'];
        $order->save();

        // Release reserved stock
        foreach ($order->items as $item) {
            $product = Product::lockForUpdate()->find($item->product_id);
            if ($product) {
                $product->releaseStock($item->quantity);
            }
        }

        Log::info('Order payment failed', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $payload['transaction_status'],
        ]);
    }
}
