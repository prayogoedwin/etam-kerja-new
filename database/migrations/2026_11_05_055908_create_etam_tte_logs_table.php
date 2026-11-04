<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etam_tte_log', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('etam_tte_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('aksi', 40)->default('tandatangani');
            $table->string('kode', 40)->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->string('judul_dokumen', 255)->nullable();
            $table->string('nama_modul', 50)->nullable();
            $table->text('pesan')->nullable();
            $table->timestamps();

            $table->index(['etam_tte_id', 'created_at']);
            $table->index(['status', 'kode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etam_tte_log');
    }
};
