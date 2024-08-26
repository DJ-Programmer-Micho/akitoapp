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
        Schema::create('variation_color_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variation_color_id')->references('id')->on('variation_colors')->onDelete('cascade')->onUpdate('cascade');
            $table->string('locale',5);
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variation_color_translations');
    }
};
