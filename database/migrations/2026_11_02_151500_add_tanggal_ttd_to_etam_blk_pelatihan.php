<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etam_blk_pelatihan', function (Blueprint $table) {
            $table->date('tanggal_ttd_dinas')->nullable()->after('jam_pelatihan');
            $table->date('tanggal_ttd_uptd')->nullable()->after('tanggal_ttd_dinas');
        });
    }

    public function down(): void
    {
        Schema::table('etam_blk_pelatihan', function (Blueprint $table) {
            $table->dropColumn(['tanggal_ttd_dinas', 'tanggal_ttd_uptd']);
        });
    }
};
