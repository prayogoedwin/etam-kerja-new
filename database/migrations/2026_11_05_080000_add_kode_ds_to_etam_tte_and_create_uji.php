<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etam_tte', function (Blueprint $table) {
            $table->string('kode_ds', 100)->nullable()->after('path_file_after');
        });

        Schema::create('etam_tte_ds_uji', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nik', 16);
            $table->string('nama_file', 255)->nullable();
            $table->string('path_file', 500)->nullable();
            $table->string('template', 100)->nullable();
            $table->string('keterangan', 255)->nullable();
            $table->string('kode_ds', 100)->nullable();
            $table->boolean('kirim_ok')->default(false);
            $table->unsignedSmallInteger('kirim_http')->nullable();
            $table->text('kirim_pesan')->nullable();
            $table->boolean('sign_ok')->nullable();
            $table->unsignedSmallInteger('sign_http')->nullable();
            $table->text('sign_pesan')->nullable();
            $table->boolean('unduh_ok')->nullable();
            $table->unsignedSmallInteger('unduh_http')->nullable();
            $table->text('unduh_pesan')->nullable();
            $table->string('path_file_after', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etam_tte_ds_uji');

        Schema::table('etam_tte', function (Blueprint $table) {
            $table->dropColumn('kode_ds');
        });
    }
};
