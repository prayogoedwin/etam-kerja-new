<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etam_tte', function (Blueprint $table) {
            $table->string('kode_dokumen', 50)->nullable()->after('kolom_status_refrensi');
            $table->index(['nama_modul', 'kode_dokumen']);
        });
    }

    public function down(): void
    {
        Schema::table('etam_tte', function (Blueprint $table) {
            $table->dropIndex(['nama_modul', 'kode_dokumen']);
            $table->dropColumn('kode_dokumen');
        });
    }
};
