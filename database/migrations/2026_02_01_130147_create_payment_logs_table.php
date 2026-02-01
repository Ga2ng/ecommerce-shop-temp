<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->string('midtrans_order_id')->nullable();
            $table->string('midtrans_transaction_id')->nullable();
            $table->string('transaction_status');
            $table->string('payment_type')->nullable();
            $table->decimal('gross_amount', 12, 2)->nullable();
            $table->text('request_payload')->nullable();
            $table->text('response_payload');
            $table->string('signature_key')->nullable();
            $table->boolean('is_valid')->default(false);
            $table->string('source')->default('webhook'); // webhook, return_url, notification
            $table->timestamps();
            
            $table->index('order_id');
            $table->index('midtrans_order_id');
            $table->index('transaction_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
