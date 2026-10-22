<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_entries', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['Pemasukan', 'Pengeluaran'])->default('Pengeluaran');
            $table->string('kategori')->default('Operasional');
            $table->string('keterangan');
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_entries');
    }
};
