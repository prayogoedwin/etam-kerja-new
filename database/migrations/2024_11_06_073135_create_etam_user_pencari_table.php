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
        Schema::create('etam_user_pencari', function (Blueprint $table) {
            $table->bigIncrements('id'); // id bigint 20 auto_increment and primary key
            $table->string('ktp', 20); // KTP varchar 20
            $table->string('email_', 50); // email_ varchar 50
            $table->string('password_', 50); // password_ varchar 50
            $table->text('toket'); // toket text
            $table->string('name', 100); // name varchar 100
            $table->string('tempat_lahir', 20); // tempat_lahir varchar 20
            $table->date('tanggal_lahir'); // tanggal_lahir date
            $table->integer('gender'); // gender int 1
            $table->integer('id_provinsi'); // id_provinsi int 4
            $table->integer('id_kota'); // id_kota int 4
            $table->integer('id_kecamatan'); // id_kecamatan int 7
            $table->string('id_desa', 10); // id_desa varchar 10
            $table->string('alamat', 200); // alamat varchar 200
            $table->string('kodepos', 5); // kodepos varchar 5
            $table->string('hp', 20); // hp varchar 20
            $table->integer('id_pendidikan'); // id_pendidikan int 2
            $table->integer('id_jurusan'); // id_jurusan int 11
            $table->integer('tahun_lulus'); // tahun_lulus int 4
            $table->char('id_status_perkawinan', 1); // id_status_perkawinan char 1
            $table->integer('id_agama'); // id_agama int 1
            $table->string('foto', 255); // foto varchar 255
            $table->integer('status_id'); // status_id int 1
            $table->integer('role_id'); // role_id int 2
            $table->timestamp('last_login')->nullable(); // last_login timestamp
            $table->integer('is_alumni_bkk'); // is_alumni_bkk int 1
            $table->integer('bkk_id'); // bkk_id int 11
            $table->integer('disabilitas'); // disabilitas int 1
            $table->integer('jenis_disabilitas'); // jenis_disabilitas int 1
            $table->string('keterangan_disabilitas', 255)->nullable(); // keterangan_disabilitas varchar 255
            $table->integer('posted_by'); // posted_by int 11
            $table->timestamps(); // created_at and updated_at timestamps
            $table->softDeletes(); // deleted_at timestamp
            $table->string('token', 255); // token varchar 255
            $table->integer('is_diterima'); // is_diterima int 1
            $table->string('medsos', 255)->nullable(); // medsos varchar 255
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_user_pencari');
    }
};
