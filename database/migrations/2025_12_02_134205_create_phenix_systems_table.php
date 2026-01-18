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
        Schema::create('phenix_systems', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // e.g. "Italian Phenix"
            $table->string('code')->unique(); // e.g. "italian", "monin"
            $table->string('base_url');      // http://192.168.100.50:8282
            $table->string('username');
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phenix_systems');
    }
};
