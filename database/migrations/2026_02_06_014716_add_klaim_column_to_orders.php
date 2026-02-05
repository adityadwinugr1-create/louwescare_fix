<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up()
{
    Schema::table('orders', function (Blueprint $table) {
        // Hanya menambah kolom klaim, tipe string, nullable
        $table->string('klaim')->nullable()->after('total_harga'); 
    });
}

public function down()
{
    Schema::table('orders', function (Blueprint $table) {
        $table->dropColumn('klaim');
    });
}
};
