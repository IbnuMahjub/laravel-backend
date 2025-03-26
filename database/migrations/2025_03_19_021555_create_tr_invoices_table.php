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
        Schema::create('tr_invoice', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pemesanan');
            $table->string('no_invoice');
            $table->string('total_harga');
            $table->string('status');
            $table->integer('user_id')->nullable();
            // $table->foreign('kode_pemesanan')->references('kode_pemesanan')->on('tr_order')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_invoices');
    }
};
