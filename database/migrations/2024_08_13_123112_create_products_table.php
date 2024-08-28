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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('updated_by_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('brand_id')->references('id')->on('brands')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('variation_id')->references('id')->on('product_variations')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('information_id')->references('id')->on('informations')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('priority')->default(0);
            $table->tinyInteger('is_spare_part')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
