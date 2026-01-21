<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('phenix_systems', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('base_url'); // http://192.168.100.50:8282

            // Secrets (store encrypted via Eloquent casts, not plaintext)
            $table->text('username');     // text because encrypted values are longer
            $table->text('password');
            $table->text('token')->nullable(); // phenixtoken

            $table->boolean('is_active')->default(true);

            // Optional operational fields
            $table->unsignedSmallInteger('timeout_seconds')->default(10);
            $table->unsignedSmallInteger('retry_times')->default(2);

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
