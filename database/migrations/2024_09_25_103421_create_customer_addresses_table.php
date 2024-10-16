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
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['Apartment', 'House', 'Office']);
            $table->string('building_name')->nullable();
            $table->string('apt_or_company')->nullable();
            $table->string('address_name');
            $table->string('floor')->nullable(); // Nullable for 'House'
            $table->string('country'); // Customer's country
            $table->string('city'); // Customer's city
            $table->string('address');
            $table->string('zip_code');
            $table->string('phone_number');
            $table->text('additional_directions')->nullable();
            $table->string('address_label')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
