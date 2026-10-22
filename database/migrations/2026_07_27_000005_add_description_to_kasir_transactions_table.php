<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kasir_transactions', function (Blueprint $table) {
            $table->text('description')->nullable()->after('vehicle_model');
        });
    }

    public function down()
    {
        Schema::table('kasir_transactions', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
