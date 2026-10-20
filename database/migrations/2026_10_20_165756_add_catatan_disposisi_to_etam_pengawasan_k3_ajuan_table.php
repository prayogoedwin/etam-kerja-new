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
        Schema::table('etam_pengawasan_k3_ajuan', function (Blueprint $table) {
            $table->text('catatan_disposisi_kadis')->nullable()->after('kadis_dispo_at');
            $table->text('catatan_disposisi_kabid')->nullable()->after('kabid_dispo_at');
            $table->text('catatan_disposisi_kasi')->nullable()->after('kasi_dispo_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etam_pengawasan_k3_ajuan', function (Blueprint $table) {
            $table->dropColumn([
                'catatan_disposisi_kadis',
                'catatan_disposisi_kabid',
                'catatan_disposisi_kasi',
            ]);
        });
    }
};
