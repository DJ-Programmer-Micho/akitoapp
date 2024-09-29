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
        Schema::create('shipping_costs', function (Blueprint $table) {
            $table->id();
            $table->decimal('first_km_cost', 8, 2)->default(2.00); // First KM Cost
            $table->decimal('additional_km_cost', 8, 2)->default(1.00); // Cost for each additional KM
            $table->decimal('free_delivery_over', 8, 2)->nullable(); // Free delivery threshold
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_costs');
    }
};
