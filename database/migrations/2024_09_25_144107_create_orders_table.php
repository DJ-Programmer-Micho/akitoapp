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
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // Foreign key referencing customers
            $table->string('first_name'); // Customer's first name
            $table->string('last_name'); // Customer's last name
            $table->string('email');
            $table->string('country'); // Customer's country
            $table->string('city'); // Customer's city
            $table->string('address'); // Customer's address
            $table->string('zip_code'); // Customer's zip code
            $table->string('phone_number'); // Customer's phone number
            $table->decimal('total_amount', 10, 2); // Total amount for the order
            $table->string('payment_method'); // Payment method (e.g., COD, digital payment)
            $table->enum('payment_status', ['pending', 'successful', 'failed'])->default('pending'); // Track payment status
            $table->enum('status', ['pending', 'shipping', 'delivered', 'canceled', 'refunded'])->default('pending'); // Order status
            $table->string('tracking_number')->nullable();
            $table->decimal('discount', 10, 2)->nullable(); // Field for discounts
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
