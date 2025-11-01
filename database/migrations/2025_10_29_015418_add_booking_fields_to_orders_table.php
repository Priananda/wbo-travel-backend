<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->date('check_in')->nullable()->after('billing_address');
            $table->date('check_out')->nullable()->after('check_in');
            $table->integer('guest')->nullable()->after('check_out');
            $table->text('extra_info')->nullable()->after('guest');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['check_in', 'check_out', 'guest', 'extra_info']);
        });
    }
};
