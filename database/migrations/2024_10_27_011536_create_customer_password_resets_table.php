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
        Schema::create('customer_password_resets', function (Blueprint $table) {
            $table->string('email')->index(); // Email of the customer requesting reset
            $table->string('token');           // Token for password reset
            $table->timestamp('created_at')->nullable(); // Timestamp to expire the token after a period
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_password_resets');
    }
};
