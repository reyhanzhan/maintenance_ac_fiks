<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_jalans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->nullable();
            $table->foreignId('rumah_sakit_id')->constrained('rumah_sakits')->cascadeOnDelete();
            $table->string('departemen')->nullable();
            $table->date('tanggal');
            $table->string('penerima')->nullable();
            $table->string('mengetahui')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('surat_jalan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_jalan_id')->constrained()->cascadeOnDelete();
            $table->integer('banyaknya')->default(1);
            $table->text('nama_barang');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_jalan_items');
        Schema::dropIfExists('surat_jalans');
    }
};
