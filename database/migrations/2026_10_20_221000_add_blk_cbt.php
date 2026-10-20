<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etam_blk_form', function (Blueprint $table) {
            $table->unsignedTinyInteger('jumlah_pilihan')->default(4)->after('deskripsi');
            $table->unsignedTinyInteger('acak_soal')->default(0)->after('jumlah_pilihan');
        });

        Schema::table('etam_blk_form_pertanyaan', function (Blueprint $table) {
            $table->string('kunci', 500)->nullable()->after('pilihan');
        });

        Schema::table('etam_blk_pelatihan', function (Blueprint $table) {
            $table->unsignedInteger('cbt_form_id')->nullable()->after('pretest_form_id');
            $table->unsignedTinyInteger('cbt_buka')->default(0)->after('cbt_form_id');
            $table->string('cbt_kode', 4)->nullable()->after('cbt_buka');
        });

        Schema::table('etam_blk_pelatihan_jawaban', function (Blueprint $table) {
            $table->string('kunci', 500)->nullable()->after('jawaban');
        });
    }

    public function down(): void
    {
        Schema::table('etam_blk_pelatihan_jawaban', function (Blueprint $table) {
            $table->dropColumn('kunci');
        });

        Schema::table('etam_blk_pelatihan', function (Blueprint $table) {
            $table->dropColumn(['cbt_form_id', 'cbt_buka', 'cbt_kode']);
        });

        Schema::table('etam_blk_form_pertanyaan', function (Blueprint $table) {
            $table->dropColumn('kunci');
        });

        Schema::table('etam_blk_form', function (Blueprint $table) {
            $table->dropColumn(['jumlah_pilihan', 'acak_soal']);
        });
    }
};
