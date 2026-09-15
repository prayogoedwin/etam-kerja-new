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
        Schema::create('etam_hi_pp_ajuan', function (Blueprint $table) {
            $table->increments('id'); // INT AUTO_INCREMENT PRIMARY KEY

            // --- Relasi utama ---
            $table->integer('jenis_ajuan')->unsigned();
            $table->integer('perusahaan_id')->unsigned();

            // --- Data umum pengajuan ---
            $table->string('surat_keputusan_izin_usaha', 255)->nullable();
            $table->string('nomor', 255)->nullable();
            $table->date('tanggal')->nullable();
            $table->string('nama_serikat_pekerja', 255)->nullable();
            $table->string('nomor_peserta_bpjs', 255)->nullable();

            // --- Jumlah pekerja ---
            $table->integer('jumlah_pekerja_pusat')->default(0);
            $table->integer('jumlah_pekerja_cabang')->default(0);

            // --- PP ---
            $table->date('tanggal_berlaku_pp_baru')->nullable();

            // --- Upah ---
            $table->integer('upah_pekerja_bulanan_min')->nullable();
            $table->integer('upah_pekerja_bulanan_max')->nullable();
            $table->integer('upah_pekerja_harian_min')->nullable();
            $table->integer('upah_pekerja_harian_max')->nullable();

            // --- Sistem hubungan kerja ---
            $table->integer('sistem_hub_kerja_tertentu')->default(0);
            $table->integer('sistem_hub_kerja_tidak_tertentu')->default(0);

            // --- Dokumen tambahan ---
            $table->text('link_gdrive_dokumen8')->nullable();

            // --- Verifikasi Admin (0 menunggu, 1 acc, 2 revisi) ---
            $table->tinyInteger('verifikasi_admin')
                  ->default(0)
                  ->comment('0 menunggu, 1 acc, 2 revisi');
            $table->integer('verifikasi_admin_by')->unsigned()->nullable();
            $table->timestamp('verifikasi_admin_at')->nullable();

            // --- Verifikasi Kasi (0 menunggu, 1 acc, 2 revisi) ---
            $table->tinyInteger('verifikasi_kasi')
                  ->default(0)
                  ->comment('0 menunggu, 1 acc, 2 revisi');
            $table->integer('verifikasi_kasi_by')->unsigned()->nullable();
            $table->timestamp('verifikasi_kasi_at')->nullable();

            // --- Keterangan revisi ---
            $table->string('keterangan_revisi_admin', 255)->nullable();
            $table->string('keterangan_revisi_kasi', 255)->nullable();
            $table->date('batas_revisi')->nullable();

            // --- Dokumen hasil akhir ---
            $table->text('dok_produk_akhir')->nullable();

            // --- Timestamps & audit ---
            $table->timestamp('created_at')->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes(); // deleted_at
            $table->integer('deleted_by')->unsigned()->nullable();

            // --- Foreign Key (opsional tapi disarankan) ---
            $table->foreign('jenis_ajuan')
                  ->references('id')->on('etam_hi_pp_jenisajuan')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');

            // --- Index untuk performa query ---
            $table->index('perusahaan_id');
            $table->index('created_by');
            $table->index('verifikasi_admin');
            $table->index('verifikasi_kasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_hi_pp_ajuan');
    }
};
