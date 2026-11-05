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
            $table->text('keterangan_revisi_admin')->nullable()->change();
            $table->text('keterangan_revisi_kasi')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etam_hi_pp_ajuan', function (Blueprint $table) {
            $table->string('keterangan_revisi_admin', 255)->nullable()->change();
            $table->string('keterangan_revisi_kasi', 255)->nullable()->change();
        });
    }
};
