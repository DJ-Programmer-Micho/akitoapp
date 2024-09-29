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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Foreign key referencing orders
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Foreign key referencing products (optional, based on your design)
            $table->integer('quantity')->default(1); // Quantity of the product ordered
            $table->string('product_name'); // Product name at the time of purchase
            $table->decimal('price', 10, 2); // Price of the product at the time of the order
            $table->decimal('total', 10, 2);
            $table->timestamps(); // Timestamps for created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
