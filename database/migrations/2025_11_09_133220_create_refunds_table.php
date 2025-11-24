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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payment_id')->constrained('payments')->cascadeOnDelete();

            $table->enum('destination', ['wallet','original']); // wallet credit or original method
            $table->unsignedBigInteger('amount_minor');
            $table->string('currency', 3)->default('IQD');

            $table->enum('status', ['requested','processed','failed'])->default('requested');
            $table->string('reason')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->json('metadata')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
