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
        Schema::table('etam_hi_pp_dokunggahpenyedia', function (Blueprint $table) {
            // Hapus FK yang salah referensi (ajuan_id → etam_hi_pp_jenisajuan)
            $table->dropForeign(['ajuan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etam_hi_pp_dokunggahpenyedia', function (Blueprint $table) {
            // Kembalikan FK ke kondisi semula (untuk rollback)
            $table->foreign('ajuan_id')
                  ->references('id')
                  ->on('etam_hi_pp_jenisajuan')
                  ->onUpdate('cascade');
        });
    }
};
