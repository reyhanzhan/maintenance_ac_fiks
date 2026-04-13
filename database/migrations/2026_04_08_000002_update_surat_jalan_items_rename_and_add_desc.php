<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->string('deskripsi_pekerjaan')->nullable()->after('departemen');
        });

        Schema::table('surat_jalan_items', function (Blueprint $table) {
            $table->renameColumn('nama_barang', 'nama_ruangan');
        });
    }

    public function down(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->dropColumn('deskripsi_pekerjaan');
        });

        Schema::table('surat_jalan_items', function (Blueprint $table) {
            $table->renameColumn('nama_ruangan', 'nama_barang');
        });
    }
};
