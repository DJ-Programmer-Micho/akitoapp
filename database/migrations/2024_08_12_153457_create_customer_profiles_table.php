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
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->references('id')->on('customers')->onDelete('cascade')->onUpdate('cascade');
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('business_module', [
                'Personal', 'Agency', 'Restaurant', 'Coffee Shop', 'Hotel', 'Other'
            ])->nullable();
            $table->string('brand_name')->nullable();
            $table->string('country');
            $table->string('city');
            $table->string('address');
            $table->string('zip_code');
            $table->string('phone_number');
            $table->string('avatar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer__profiles');
    }
};
