<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etam_pjk3_suket', function (Blueprint $table) {
            $table->unsignedBigInteger('ahli_k3_kategori_id')->nullable()->after('kategori_id');
            $table->foreign('ahli_k3_kategori_id')->references('id')->on('etam_pengawasan_k3_kategori');
            $table->index('ahli_k3_kategori_id');
        });

        Schema::create('etam_pjk3_suket_perangkat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('suket_id');
            $table->unsignedBigInteger('jenis_id');
            $table->string('nama', 255);
            $table->string('merk', 255)->nullable();
            $table->string('jumlah', 50)->nullable();
            $table->string('lokasi', 255)->nullable();
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->timestamps();

            $table->foreign('suket_id')->references('id')->on('etam_pjk3_suket')->cascadeOnDelete();
            $table->foreign('jenis_id')->references('id')->on('etam_pengawasan_k3_jenis');
            $table->index(['suket_id', 'jenis_id']);
            $table->index('jenis_id');
        });

        Schema::create('etam_pjk3_suket_uji', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('suket_id');
            $table->unsignedBigInteger('perangkat_id');
            $table->string('item', 255);
            $table->string('status', 30)->nullable();
            $table->string('hasil', 500)->nullable();
            $table->unsignedSmallInteger('urutan')->default(1);
            $table->timestamps();

            $table->foreign('suket_id')->references('id')->on('etam_pjk3_suket')->cascadeOnDelete();
            $table->foreign('perangkat_id')->references('id')->on('etam_pjk3_suket_perangkat')->cascadeOnDelete();
            $table->index(['suket_id', 'perangkat_id']);
            $table->index('perangkat_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etam_pjk3_suket_uji');
        Schema::dropIfExists('etam_pjk3_suket_perangkat');

        Schema::table('etam_pjk3_suket', function (Blueprint $table) {
            $table->dropForeign(['ahli_k3_kategori_id']);
            $table->dropIndex(['ahli_k3_kategori_id']);
            $table->dropColumn('ahli_k3_kategori_id');
        });
    }
};
