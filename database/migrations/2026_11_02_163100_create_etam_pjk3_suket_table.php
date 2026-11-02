<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etam_pjk3_suket', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pjk3_penyedia_id');
            $table->unsignedBigInteger('perusahaan_id');
            $table->unsignedBigInteger('kategori_id');
            $table->unsignedBigInteger('jenis_id')->nullable();
            $table->string('nama_alat', 255)->nullable();
            $table->string('lokasi_alat', 255)->nullable();
            $table->string('kapasitas_alat', 255)->nullable();
            $table->integer('jumlah_unit')->default(1);
            $table->json('dokumen')->nullable();
            $table->unsignedTinyInteger('status')->default(0);
            $table->unsignedBigInteger('pengawas_id')->nullable();
            $table->unsignedBigInteger('kasi_id')->nullable();
            $table->text('catatan_disposisi')->nullable();
            $table->date('tanggal_pemeriksaan')->nullable();
            $table->text('hasil_analisis')->nullable();
            $table->text('rekomendasi')->nullable();
            $table->unsignedTinyInteger('is_layak')->nullable();
            $table->string('nomor_suket', 80)->nullable();
            $table->timestamp('ttd_pengawas_at')->nullable();
            $table->timestamp('ttd_kadis_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('pjk3_penyedia_id')->references('id')->on('users');
            $table->foreign('perusahaan_id')->references('id')->on('users');
            $table->foreign('kategori_id')->references('id')->on('etam_pengawasan_k3_kategori');
            $table->foreign('jenis_id')->references('id')->on('etam_pengawasan_k3_jenis');
            $table->foreign('pengawas_id')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etam_pjk3_suket');
    }
};
