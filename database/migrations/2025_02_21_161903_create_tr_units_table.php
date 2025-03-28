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
        Schema::create('tr_unit', function (Blueprint $table) {
            $table->id();
            $table->integer('property_id');
            $table->string('name_property');
            $table->enum('tipe', ['Deluxe', 'Standard', 'Suite']);
            $table->string('harga_unit');
            $table->integer('jumlah_kamar');
            $table->string('deskripsi');
            $table->json('images')->nullable();
            $table->integer('is_delete')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_units');
    }
};
