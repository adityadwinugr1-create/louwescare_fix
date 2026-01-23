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
        Schema::table('orders', function (Blueprint $table) {
            // Menambahkan kolom kasir_keluar (boleh kosong/nullable)
            // Posisinya diletakkan setelah kolom 'kasir' (CS Masuk) agar rapi
            $table->string('kasir_keluar')->nullable()->after('kasir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('kasir_keluar');
        });
    }
};