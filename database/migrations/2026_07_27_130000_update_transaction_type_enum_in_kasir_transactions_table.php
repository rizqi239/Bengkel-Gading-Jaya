<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::statement("ALTER TABLE `kasir_transactions` MODIFY `transaction_type` ENUM('Servis + Sparepart', 'Servis Mesin', 'Tune Up', 'Service CVT', 'BoreUp', 'Servis', 'Penjualan Barang') NOT NULL DEFAULT 'Servis + Sparepart'");
    }

    public function down()
    {
        DB::statement("ALTER TABLE `kasir_transactions` MODIFY `transaction_type` ENUM('Servis', 'Penjualan Barang', 'Servis + Sparepart') NOT NULL DEFAULT 'Servis'");
    }
};
