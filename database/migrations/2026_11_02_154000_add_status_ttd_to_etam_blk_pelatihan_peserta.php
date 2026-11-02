<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etam_blk_pelatihan_peserta', function (Blueprint $table) {
            $table->unsignedTinyInteger('status_ttd')->default(0)->after('status_pendaftaran');
            $table->timestamp('ttd_uptd_at')->nullable()->after('status_ttd');
            $table->timestamp('ttd_dinas_at')->nullable()->after('ttd_uptd_at');
        });
    }

    public function down(): void
    {
        Schema::table('etam_blk_pelatihan_peserta', function (Blueprint $table) {
            $table->dropColumn(['status_ttd', 'ttd_uptd_at', 'ttd_dinas_at']);
        });
    }
};
