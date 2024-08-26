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
        Schema::create('variation_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('updated_by_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('code')->default(0);
            $table->integer('priority')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variation_sizes');
    }
};
