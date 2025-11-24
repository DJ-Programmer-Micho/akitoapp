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
        Schema::create('customer_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('currency', 3)->default('IQD');
            $table->unsignedBigInteger('balance_minor')->default(0); // integer money
            $table->unsignedBigInteger('locked_minor')->default(0);  // optional holds
            $table->unsignedInteger('version')->default(1);          // optimistic concurrency
            $table->timestamps();

            $table->unique('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_wallets');
    }
};
