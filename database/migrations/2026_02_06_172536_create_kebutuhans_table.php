<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kebutuhans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kebutuhan');
            $table->string('stok_terakhir'); // Menggunakan string jaga-jaga user input "5 pcs"
            $table->date('tanggal');
            $table->timestamps(); // Created_at & Updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('kebutuhans');
    }
};