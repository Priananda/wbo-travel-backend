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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('payment_gateway')->default('midtrans'); // ganti nanti pakai doku
            $table->string('payment_id')->nullable(); // trx id from DOKU
            $table->decimal('amount', 12, 2)->nullable();
            $table->enum('status', [
                'pending',
                'settlement',
                'cancelled',
                'deny',
                'expire'
            ])->default('pending');
            $table->json('payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
