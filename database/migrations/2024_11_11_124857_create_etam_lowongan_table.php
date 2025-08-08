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
        Schema::create('etam_lowongan', function (Blueprint $table) {
            $table->increments('id');  // id int 11 primary_key
            $table->string('nama_perusahaan_bybkk', 255)->nullable(); // varchar 255 nullable
            $table->integer('jabatan_id')->length(11);  // int 11
            $table->integer('sektor_id')->length(11);  // int 11
            $table->date('tanggal_start'); // date
            $table->date('tanggal_end');   // date
            $table->string('judul_lowongan', 255);  // varchar 255
            $table->integer('kabkota_id')->length(11);  // int 11
            $table->text('lokasi_penempatan_text');  // text
            $table->integer('jumlah_pria')->length(11);  // int 11
            $table->integer('jumlah_wanita')->length(11);  // int 11
            $table->text('deskripsi')->nullable();  // text nullable
            $table->tinyInteger('status_id')->default(0);  // int 1 default value 0
            $table->integer('pendidikan_id')->length(3);  // int 3
            $table->integer('jurusan_id')->length(11);  // int 11
            $table->char('marital_id', 1);  // varchar 1
            $table->integer('acc_by')->length(11);  // int 11
            $table->integer('acc_by_role')->length(11);  // int 11
            $table->timestamp('acc_at')->nullable();  // timestamp nullable
            $table->tinyInteger('is_lowongan_bkk')->default(0);  // int 1 default value 0
            $table->tinyInteger('is_lowongan_ln')->default(0);  // int 1 default value 0
            $table->tinyInteger('is_lowongan_disabilitas')->default(0);  // int 1 default value 0
            $table->string('nama_petugas', 255)->nullable();  // varchar 255 nullable
            $table->string('nip_petugas', 255)->nullable();  // varchar 255 nullable
            $table->string('kompetensi', 255)->nullable();  // varchar 255 nullable
            $table->integer('posted_by')->length(11);  // int 11
            $table->timestamps();  // created_at, updated_at otomatis
            $table->softDeletes();  // deleted_at timestamp (untuk soft delete)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_lowongan');
    }
};
