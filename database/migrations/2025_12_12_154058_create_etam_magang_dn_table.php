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
        Schema::create('etam_magang_dn', function (Blueprint $table) {
            $table->id();
            $table->integer('jenis_penyelenggara')->comment('0: pemerintah, 1: swasta');
            $table->unsignedBigInteger('id_penyelenggara')->nullable()->comment('user_id');
            $table->string('nama_magang')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->string('penyelenggara')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('poster')->nullable();
            $table->integer('tipe_magang')->comment('0: online, 1: offline');
            $table->string('kota', 10)->default('64')->comment('default 64, jika offline akan terisi 4 digit (6401, dst)');
            $table->string('lokasi_penyelenggaraan')->nullable();
            $table->date('tanggal_open_pendaftaran_tenant')->nullable();
            $table->integer('tipe_partnership')->default(0)->comment('0: tertutup, 1: open (perusahaan bisa daftar)');
            $table->date('tanggal_close_pendaftaran_tenant')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->integer('status_verifikasi')->default(0);
            $table->unsignedBigInteger('id_verifikator')->nullable()->comment('user_id');
            $table->integer('status')->default(1);
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by')->nullable();

            // Indexes
            $table->index('jenis_penyelenggara');
            $table->index('tipe_magang');
            $table->index('status');
            $table->index('status_verifikasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_magang_dn');
    }
};
