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
        Schema::table('kasir_transactions', function (Blueprint $table) {
            $table->enum('payment_method', ['Tunai', 'Dana', 'GoPay', 'Bank BCA', 'Bank Mandiri'])->default('Tunai')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kasir_transactions', function (Blueprint $table) {
            $table->enum('payment_method', ['Tunai', 'Transfer', 'QRIS'])->default('Tunai')->change();
        });
    }
};
