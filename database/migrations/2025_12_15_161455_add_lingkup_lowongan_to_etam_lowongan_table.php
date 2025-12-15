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
            $table->tinyInteger('lingkup_lowongan')
                  ->default(0)
                  ->after('tipe_lowongan')
                  ->comment('0 = kabkota, 1 = provinsi, 2 = nasional');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etam_lowongan', function (Blueprint $table) {
            $table->dropColumn('lingkup_lowongan');
        });
    }
};
