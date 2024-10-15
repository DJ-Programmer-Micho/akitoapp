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
        Schema::create('zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('delivery_team')->references('id')->on('driver_teams')->onDelete('cascade');;
            $table->string('digit_payment');
            $table->string('cod_payment');
            $table->string('status');
            $table->json('coordinates'); // Store the polygon coordinates as JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zones');
    }
};
