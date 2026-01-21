<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phenix_sync_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('phenix_system_id')->nullable();
            $table->string('system_code')->nullable(); // italian, monin...

            $table->integer('matched')->default(0);
            $table->integer('updated')->default(0);
            $table->integer('changes')->default(0);

            $table->string('xlsx_path')->nullable(); // sync_log/italian/2026-01-19/price_sync_...
            $table->json('meta')->nullable();

            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            $table->index(['phenix_system_id', 'synced_at']);

            // Optional but recommended FK
            $table->foreign('phenix_system_id')
                ->references('id')->on('phenix_systems')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phenix_sync_logs');
    }
};
