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
        Schema::create('paket_tour_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_tour_id')->constrained('paket_tours')->onDelete('cascade');
            $table->integer('day');
            $table->string('title');
            $table->json('activities')->nullable(); // simpan array activities
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_tour_plans');
    }
};
