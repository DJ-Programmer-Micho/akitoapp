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
            $table->foreignId('color_id')->references('id')->on('variation_colors')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('size_id')->references('id')->on('variation_sizes')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('material_id')->references('id')->on('variation_materials')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('capacity_id')->references('id')->on('variation_capacities')->onDelete('cascade')->onUpdate('cascade');
            $table->string('sku')->nullable()->unique();
            $table->integer('price');
            $table->integer('discount')->nullable();
            $table->integer('on_stock');
            $table->tinyInteger('status')->default(1);

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
