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
        Schema::create('ticker_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticker_id')->references('id')->on('tickers')->onDelete('cascade')->onUpdate('cascade');
            $table->string('locale',5);
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticker_translations');
    }
};
