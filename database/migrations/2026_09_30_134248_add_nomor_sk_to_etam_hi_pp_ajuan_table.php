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
            $table->string('nomor_sk', 255)->nullable()->after('tanggal_berlaku_pp_baru');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etam_hi_pp_ajuan', function (Blueprint $table) {
            $table->dropColumn('nomor_sk');
        });
    }
};
