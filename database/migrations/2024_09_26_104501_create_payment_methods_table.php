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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Payment method name
            $table->boolean('active')->default(true); // Whether the method is active
            $table->string('addon_identifier')->nullable(); // External identifier (for APIs)
            $table->string('online')->nullable(); // External identifier (for APIs)
            $table->decimal('transaction_fee', 8, 2)->default(0); // Transaction fee (percentage or amount)
            $table->string('currency', 3)->nullable(); // Currency code (e.g., USD, EUR)
            $table->timestamps(); // Laravel's created_at and updated_at fields
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
