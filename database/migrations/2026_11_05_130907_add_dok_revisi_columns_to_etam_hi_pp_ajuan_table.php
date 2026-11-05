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
        Schema::table('etam_hi_pp_ajuan', function (Blueprint $table) {
            // Tambah SETELAH keterangan_revisi_kasi
            $table->string('dok_revisi_admin', 255)->nullable()->after('keterangan_revisi_kasi');
            $table->string('dok_revisi_kasi', 255)->nullable()->after('dok_revisi_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etam_hi_pp_ajuan', function (Blueprint $table) {
            $table->dropColumn(['dok_revisi_admin', 'dok_revisi_kasi']);
        });
    }
};
