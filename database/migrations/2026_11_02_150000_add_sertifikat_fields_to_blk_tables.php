<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etam_blk_pelatihan', function (Blueprint $table) {
            $table->string('kejuruan', 255)->nullable()->after('nama_pelatihan');
            $table->string('angkatan', 50)->nullable()->after('kejuruan');
            $table->unsignedInteger('jam_pelatihan')->nullable()->after('angkatan');
        });

        Schema::table('etam_blk_pelatihan_peserta', function (Blueprint $table) {
            $table->string('nomor_sertifikat', 100)->nullable()->after('status_kerja');
        });
    }

    public function down(): void
    {
        Schema::table('etam_blk_pelatihan', function (Blueprint $table) {
            $table->dropColumn(['kejuruan', 'angkatan', 'jam_pelatihan']);
        });

        Schema::table('etam_blk_pelatihan_peserta', function (Blueprint $table) {
            $table->dropColumn('nomor_sertifikat');
        });
    }
};
