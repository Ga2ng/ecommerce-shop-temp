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
        Schema::table('orders', function (Blueprint $table) {
            // Drop existing foreign key constraint
            $table->dropForeign(['user_id']);
        });
        
        Schema::table('orders', function (Blueprint $table) {
            // Make user_id nullable to support guest checkout
            $table->foreignId('user_id')->nullable()->change();
        });
        
        Schema::table('orders', function (Blueprint $table) {
            // Re-add foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['user_id']);
        });
        
        Schema::table('orders', function (Blueprint $table) {
            // Revert to non-nullable (only if no null values exist)
            $table->foreignId('user_id')->nullable(false)->change();
        });
        
        Schema::table('orders', function (Blueprint $table) {
            // Re-add foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
