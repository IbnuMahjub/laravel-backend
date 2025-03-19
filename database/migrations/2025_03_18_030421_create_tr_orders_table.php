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
        Schema::create('tr_order', function (Blueprint $table) {
            $table->id();
            $table->integer('property_id');
            $table->integer('unit_id');
            $table->integer('user_id')->nullable();
            $table->string('username')->nullable();
            $table->string('name_property');
            $table->string('harga_unit');
            $table->string('jumlah_kamar');
            $table->string('tanggal_check_in');
            $table->string('tanggal_check_out');
            $table->string('jumlah_hari');
            $table->text('catatan')->nullable();
            $table->string('status');
            $table->string('total_harga');
            $table->string('kode_pemesanan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_orders');
    }
};
