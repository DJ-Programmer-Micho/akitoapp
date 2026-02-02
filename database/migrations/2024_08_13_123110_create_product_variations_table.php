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
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id')->nullable();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->string('sku')->nullable()->unique();
            $table->unsignedBigInteger('phenix_system_id')->nullable();
            $table->string('keywords')->nullable();
            $table->decimal('price', 12, 0);
            $table->decimal('discount', 12, 0)->nullable();
            $table->integer('stock')->default(1);
            $table->integer('order_limit')->default(1);
            $table->integer('on_sale');
            $table->integer('featured');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
