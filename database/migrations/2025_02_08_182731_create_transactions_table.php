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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('stripe_session_id')->nullable();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->foreignId('order_id')->constrained()->nullable()->onDelete('cascade'); // Ensure consistency
            $table->unsignedBigInteger('amount_minor');
            $table->string('provider'); // Areeba, ZainCash, FIB
            $table->decimal('amount', 12, 2); // Ensure proper data type for amount
            $table->string('currency', 3)->default('IQD'); // 3-letter ISO currency code
            $table->string('status')->default('pending'); // pending, success, failed
            $table->json('response')->nullable(); // Rename `transactions_data` to `response` for clarity
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
