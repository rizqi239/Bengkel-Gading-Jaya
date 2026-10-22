<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kasir_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable();
            $table->enum('transaction_type', ['Servis + Sparepart', 'Servis Mesin', 'Tune Up', 'Service CVT', 'BoreUp', 'Penjualan Barang'])->default('Servis + Sparepart');
            $table->string('plate_number')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->json('items')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('payment_method', ['Tunai', 'Dana', 'GoPay', 'Bank BCA', 'Bank Mandiri'])->default('Tunai');
            $table->enum('status', ['Draft', 'Selesai', 'Dibatalkan'])->default('Draft');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kasir_transactions');
    }
};
