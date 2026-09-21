<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etam_blk', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tipe_lembaga')->default(1)->comment('1 provinsi, 2 kabkota');
            $table->string('nama_lembaga', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('whatsapp', 255)->nullable();
            $table->string('telepon', 255)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('instagram', 255)->nullable();
            $table->unsignedInteger('provinsi_id')->default(64);
            $table->unsignedInteger('kabkota_id')->nullable();
            $table->text('alamat_lengkap')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('users_blk', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('tipe_akun')->default(3)->comment('0 admin all blk, 1 admin blk provinsi, 2 admin blk kabkota, 3 admin masing-masing blk, 4 officer blk');
            $table->unsignedInteger('blk_id')->default(0)->comment('jika tipe akun 0, maka blk_id boleh 0');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('etam_blk_pelatihan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pelatihan_untuk')->default(0)->comment('0 pencari kerja, 1 penyedia kerja');
            $table->unsignedInteger('blk_id')->nullable();
            $table->string('nama_pelatihan', 255)->nullable();
            $table->unsignedInteger('sumber_pembiayaan')->default(0)->comment('0 APBD, 1 DBHCHT, 2 APBN');
            $table->date('tanggal_pendaftaran')->nullable();
            $table->date('tanggal_pendaftaran_selesai')->nullable();
            $table->date('tanggal_pelaksanaan')->nullable();
            $table->date('tanggal_pelaksanaan_selesai')->nullable();
            $table->unsignedInteger('tipe_pelatihan')->nullable()->comment('0 offline, 1 online, 2 hybrid, 3 MTU');
            $table->string('info_lokasi', 255)->nullable();
            $table->unsignedInteger('status')->default(0)->comment('0 draft, 1 aktif, 2 tutup, 3 selesai');
            $table->string('slug', 255)->nullable();
            $table->text('deskripsi')->nullable();
            $table->text('poster')->nullable();
            $table->unsignedInteger('template_sertifikat')->nullable()->comment('1-3');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('etam_blk_pelatihan_fasilitas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('blk_pelatihan_id')->nullable();
            $table->text('fasilitas')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('etam_blk_pelatihan_syarat', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('blk_pelatihan_id')->nullable();
            $table->text('persyaratan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('etam_blk_pelatihan_pertanyaan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('blk_pelatihan_id')->nullable();
            $table->unsignedInteger('jenis_pertanyaan_id')->nullable();
            $table->text('pertanyaan')->nullable();
            $table->text('pilihan')->nullable();
            $table->text('kunci_jawaban')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('etam_blk_pelatihan_peserta', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('blk_pelatihan_id')->nullable();
            $table->unsignedInteger('pencari_id')->nullable();
            $table->unsignedInteger('status_pendaftaran')->nullable();
            $table->unsignedInteger('status_kerja')->nullable();
            $table->text('alasan_status')->nullable();
            $table->string('name', 255)->nullable();
            $table->string('ktp', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('hp', 100)->nullable();
            $table->string('tempat_lahir', 255)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->char('gender', 1)->nullable();
            $table->unsignedInteger('id_provinsi')->nullable();
            $table->unsignedInteger('id_kota')->nullable();
            $table->unsignedInteger('id_kecamatan')->nullable();
            $table->string('id_desa', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->unsignedInteger('id_pendidikan')->nullable();
            $table->unsignedInteger('sekolahuniv')->nullable();
            $table->unsignedInteger('id_jurusan')->nullable();
            $table->string('id_status_perkawinan', 10)->nullable();
            $table->unsignedInteger('sumber_informasi')->default(100);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('etam_blk_pelatihan_peserta_perusahaan', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('blk_pelatihan_id')->nullable();
            $table->unsignedInteger('perusahaan_id')->nullable();
            $table->unsignedInteger('status_pendaftaran')->nullable();
            $table->text('alasan_status')->nullable();
            $table->string('name', 100)->nullable();
            $table->string('nib', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('hp', 20)->nullable();
            $table->unsignedInteger('id_provinsi')->nullable();
            $table->unsignedInteger('id_kota')->nullable();
            $table->unsignedInteger('id_kecamatan')->nullable();
            $table->string('id_desa', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->unsignedInteger('sumber_informasi')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('etam_blk_pelatihan_jawaban', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('blk_pelatihan_id')->nullable();
            $table->unsignedInteger('blk_pertanyaan_id')->nullable();
            $table->unsignedInteger('blk_peserta_id')->nullable();
            $table->unsignedInteger('pencari_id')->nullable();
            $table->text('pertanyaan')->nullable();
            $table->text('pilihan')->nullable();
            $table->text('jawaban')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etam_blk_pelatihan_jawaban');
        Schema::dropIfExists('etam_blk_pelatihan_peserta_perusahaan');
        Schema::dropIfExists('etam_blk_pelatihan_peserta');
        Schema::dropIfExists('etam_blk_pelatihan_pertanyaan');
        Schema::dropIfExists('etam_blk_pelatihan_syarat');
        Schema::dropIfExists('etam_blk_pelatihan_fasilitas');
        Schema::dropIfExists('etam_blk_pelatihan');
        Schema::dropIfExists('users_blk');
        Schema::dropIfExists('etam_blk');
    }
};
