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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained('customer_wallets')->cascadeOnDelete();

            $table->enum('direction', ['credit','debit']);
            $table->unsignedBigInteger('amount_minor');
            $table->string('currency', 3)->default('IQD');

            $table->nullableMorphs('source'); // source_type, source_id
            $table->string('reason')->nullable(); // 'order_refund', 'manual_topup', 'wallet_payment', etc.
            $table->json('meta')->nullable();

            $table->unsignedBigInteger('balance_after_minor'); // snapshot after txn
            $table->string('idempotency_key')->nullable()->index();

            $table->timestamps();

            $table->index(['wallet_id','created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
