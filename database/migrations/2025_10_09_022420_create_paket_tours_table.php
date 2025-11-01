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
    Schema::create('paket_tours', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->decimal('price', 12, 2);
    $table->integer('stock')->default(0);
    $table->string('location')->nullable();
    $table->string('image')->nullable();
    $table->boolean('active')->default(true);

    // field baru
    $table->integer('duration_days')->default(5);
    $table->integer('duration_nights')->default(4);
    $table->integer('feature_duration_days')->default(5); // featured otomatis selama 5 hari
    $table->integer('minimum_age')->default(0);
    $table->string('pickup_location')->nullable();

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_tours');
    }
};
