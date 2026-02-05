<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel untuk dashboard analisis: satu baris per transaksi berhasil (settlement/capture).
     */
    public function up(): void
    {
        Schema::create('transaction_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('order_number', 64)->index();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');

            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 20)->nullable();

            $table->decimal('subtotal', 14, 2);
            $table->decimal('shipping_cost', 14, 2)->default(0);
            $table->decimal('total', 14, 2);

            $table->string('payment_type', 64)->nullable()->index();
            $table->string('payment_method_label', 128)->nullable();
            $table->string('midtrans_order_id', 64)->nullable()->index();
            $table->string('midtrans_transaction_id', 64)->nullable()->index();

            $table->timestamp('paid_at')->nullable()->index();
            $table->timestamps();

            $table->unique('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_records');
    }
};
