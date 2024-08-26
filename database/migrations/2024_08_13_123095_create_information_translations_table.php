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
        Schema::create('information_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('information_id')->references('id')->on('informations')->onDelete('cascade')->onUpdate('cascade');
            $table->string('locale',5);
            $table->longText('description')->nullable();
            $table->longText('addition')->nullable();
            $table->json('question_and_answer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('information_translations');
    }
};
