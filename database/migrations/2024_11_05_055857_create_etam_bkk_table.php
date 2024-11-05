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
        Schema::create('etam_bkk', function (Blueprint $table) {
            $table->increments('id'); // id int 11 auto_increment
            $table->string('no_sekolah', 100);
            $table->unsignedSmallInteger('id_sekolah'); // id_sekolah int 2
            $table->string('name', 100);
            $table->string('website', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('alamat', 100);
            $table->unsignedMediumInteger('id_provinsi'); // id_prinvisi int 4
            $table->unsignedMediumInteger('id_kota'); // id_kota int 4
            $table->unsignedBigInteger('id_kecamatan'); // id_kecamatan int 7
            $table->unsignedBigInteger('id_desa'); // id_desa int 10
            $table->string('kodepos', 5);
            $table->string('nama_bkk', 100);
            $table->string('no_bkk', 100);
            $table->date('tanggal_aktif_bkk');
            $table->date('tanggal_non_aktif_bkk')->nullable();
            $table->string('telpon', 13)->nullable();
            $table->string('hp', 13)->nullable();
            $table->string('contact_person', 50)->nullable();
            $table->string('jabatan', 50)->nullable();
            $table->text('foto')->nullable();
            $table->timestamp('tanggal_register')->nullable();
            $table->unsignedTinyInteger('role_id'); // role_id int 1
            $table->string('username_', 255);
            $table->string('password_', 255);
            $table->unsignedTinyInteger('status_id'); // status_id int 1
            $table->timestamp('last_login')->nullable();
            $table->text('foto_logo')->nullable();
            $table->timestamps(); // created_at, updated_at
            $table->string('kode_verifikasi', 6)->nullable();
            $table->timestamp('kode_verifikasi_waktu')->nullable();
            $table->softDeletes(); // deleted_at timestamp
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_bkk');
    }
};
