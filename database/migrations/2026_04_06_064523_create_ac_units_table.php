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
        Schema::create('ac_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rumah_sakit_id')->constrained('rumah_sakits')->cascadeOnDelete();
            $table->string('gedung')->nullable();
            $table->string('jenis_ac');
            $table->string('merk_ac');
            $table->string('kapasitas_pk');
            $table->string('ruangan');
            $table->string('lantai');
            $table->integer('frekuensi_cuci')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ac_units');
    }
};
