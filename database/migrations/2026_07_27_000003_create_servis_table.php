<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('servis', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number');
            $table->string('vehicle_model');
            $table->string('owner_name');
            $table->string('mechanic')->nullable();
            $table->enum('service_type', ['kecil', 'besar'])->default('kecil');
            $table->text('complaint')->nullable();
            $table->enum('status', ['Antrean', 'Dikerjakan', 'Menunggu Part', 'Selesai'])->default('Antrean');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('servis');
    }
};
