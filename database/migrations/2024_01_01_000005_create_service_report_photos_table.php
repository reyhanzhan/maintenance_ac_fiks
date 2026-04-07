<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_report_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_report_item_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('photo_path');
            $table->string('tipe')->default('general'); // 'general' or 'tidak_normal'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_report_photos');
    }
};
