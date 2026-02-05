<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('point_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->integer('amount'); // +1 (Dapat) atau -8 (Tukar)
            $table->string('type'); // 'earn' (Dapat) atau 'redeem' (Tukar)
            $table->string('description')->nullable(); // Keterangan tambahan
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('point_histories');
    }
};