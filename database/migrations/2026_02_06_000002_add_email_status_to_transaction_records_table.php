<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Status pengiriman email struk: pending = belum terkirim, sent = sudah terkirim.
     */
    public function up(): void
    {
        Schema::table('transaction_records', function (Blueprint $table) {
            $table->string('email_status', 20)->default('pending')->after('paid_at')->index();
            $table->timestamp('email_sent_at')->nullable()->after('email_status');
        });
    }

    public function down(): void
    {
        Schema::table('transaction_records', function (Blueprint $table) {
            $table->dropColumn(['email_status', 'email_sent_at']);
        });
    }
};
