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
        Schema::create('coming_soon_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coming_soon_id')->references('id')->on('coming_soons')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('coming_soon_translations');
    }
};
