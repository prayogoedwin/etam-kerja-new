<?php

use App\Models\EtamStruktur;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etam_tte', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id_tujuan')->nullable()->after('id_struktur');
            $table->index('user_id_tujuan');
        });

        $kadisId = User::idKepalaDinas();
        DB::table('etam_tte')
            ->where('nama_modul', 'blk')
            ->where(function ($q) {
                $q->whereNull('id_struktur')->orWhere('id_struktur', '');
            })
            ->update(array_filter([
                'id_struktur' => EtamStruktur::KODE_DINAS,
                'user_id_tujuan' => $kadisId,
            ], fn ($value) => $value !== null));
    }

    public function down(): void
    {
        Schema::table('etam_tte', function (Blueprint $table) {
            $table->dropIndex(['user_id_tujuan']);
            $table->dropColumn('user_id_tujuan');
        });
    }
};
