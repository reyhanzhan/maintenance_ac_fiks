<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_jalan_items', function (Blueprint $table) {
            $table->string('type_ac')->nullable()->after('nama_ruangan');
            $table->string('pk')->nullable()->after('type_ac');
        });
    }

    public function down(): void
    {
        Schema::table('surat_jalan_items', function (Blueprint $table) {
            $table->dropColumn(['type_ac', 'pk']);
        });
    }
};
