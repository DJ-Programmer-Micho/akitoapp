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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->nullable()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->nullable()->onDelete('cascade');
            $table->unsignedBigInteger('amount_minor');
            $table->string('currency')->default('IQD');
            $table->string('method'); // COD, Areeba, ZainCash, FIB
            $table->string('status')->default('pending'); // pending, paid, failed, refunded
            $table->string('provider')->nullable();           // 'FIB', 'Areeba', 'ZainCash', etc.
            $table->string('provider_payment_id')->nullable();
            $table->string('idempotency_key')->nullable()->index();
            $table->string('type')->default('order');
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
