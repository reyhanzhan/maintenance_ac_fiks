<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rumah_sakits', function (Blueprint $table) {
            $table->string('mengetahui_surat_jalan')->nullable()->after('koordinator_lapangan');
        });
    }

    public function down(): void
    {
        Schema::table('rumah_sakits', function (Blueprint $table) {
            $table->dropColumn('mengetahui_surat_jalan');
        });
    }
};
