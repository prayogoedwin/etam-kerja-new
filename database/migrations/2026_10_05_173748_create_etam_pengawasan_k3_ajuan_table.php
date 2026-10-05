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
        Schema::create('etam_pengawasan_k3_ajuan', function (Blueprint $table) {
            $table->id(); // int 11, primary key, auto increment

            // Foreign keys
            $table->unsignedBigInteger('penyedia_id');
            $table->unsignedBigInteger('kategori_id');
            $table->unsignedBigInteger('jenis_id');

            // Detail alat / objek K3
            $table->string('nama_alat', 255);
            $table->string('lokasi_alat', 255);
            $table->string('kapasitas_alat', 255);
            $table->integer('jumlah_unit')->default(1);
            $table->text('keterangan')->nullable();
            $table->text('dok_unggah_penyedia')->nullable();

            // Status Disposisi Berjenjang (0: Menunggu, 1: Dispo)
            $table->tinyInteger('is_kadis_dispo')->default(0)->comment('0 menunggu, 1 dispo');
            $table->timestamp('kadis_dispo_at')->nullable();

            $table->tinyInteger('is_kabid_dispo')->default(0)->comment('0 menunggu, 1 dispo');
            $table->timestamp('kabid_dispo_at')->nullable();

            $table->tinyInteger('is_kasi_dispo')->default(0)->comment('0 menunggu, 1 dispo');
            $table->timestamp('kasi_dispo_at')->nullable();

            // Timestamps & Soft Deletes
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();

            // Definisi Constraint Foreign Key
            $table->foreign('penyedia_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('kategori_id')->references('id')->on('etam_pengawasan_k3_kategori')->onDelete('cascade');
            $table->foreign('jenis_id')->references('id')->on('etam_pengawasan_k3_jenis')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_pengawasan_k3_ajuan');
    }
};
