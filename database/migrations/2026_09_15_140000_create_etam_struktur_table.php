<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etam_struktur', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('tipe')->default(1)->comment('1=provinsi, 2=kabkota');
            $table->string('kode_lokasi', 10)->comment('kode provinsi atau kabkota');
            $table->string('kode_bidang', 20);
            $table->string('nama');
            $table->string('slug');
            $table->timestamps();

            $table->unique(['tipe', 'kode_lokasi', 'kode_bidang'], 'etam_struktur_lokasi_kode_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etam_struktur');
    }
};
