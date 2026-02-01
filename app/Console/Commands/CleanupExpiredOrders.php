<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupExpiredOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:cleanup-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup expired orders and release reserved stock';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of expired orders...');

        $expiredOrders = Order::where('status', 'pending_payment')
            ->where('expires_at', '<', now())
            ->get();

        $count = 0;

        foreach ($expiredOrders as $order) {
            try {
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

                $count++;
                $this->line("Expired order: {$order->order_number}");

            } catch (\Exception $e) {
                $this->error("Failed to expire order {$order->order_number}: " . $e->getMessage());
            }
        }

        $this->info("Cleanup completed. {$count} orders expired.");
        return Command::SUCCESS;
    }
}
