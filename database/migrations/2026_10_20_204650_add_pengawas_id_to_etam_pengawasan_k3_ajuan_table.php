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
            $table->unsignedBigInteger('pengawas_id')->nullable()->after('kasi_dispo_at');
            $table->foreign('pengawas_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etam_pengawasan_k3_ajuan', function (Blueprint $table) {
            $table->dropForeign(['pengawas_id']);
            $table->dropColumn('pengawas_id');
        });
    }
};
