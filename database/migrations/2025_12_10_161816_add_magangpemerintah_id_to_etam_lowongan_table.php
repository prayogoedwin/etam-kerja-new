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
        Schema::table('etam_lowongan', function (Blueprint $table) {
            $table->integer('magangpemerintah_id')
                ->nullable()
                ->after('jobfair_id')
                ->comment('ID Magang Pemerintah (null jika lowongan umum)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etam_lowongan', function (Blueprint $table) {
            $table->dropColumn('magangpemerintah_id');
        });
    }
};
