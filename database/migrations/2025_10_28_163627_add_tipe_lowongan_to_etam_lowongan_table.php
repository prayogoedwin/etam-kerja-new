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
            if (!Schema::hasColumn('etam_lowongan', 'tipe_lowongan')) {
                $table->integer('tipe_lowongan')
                      ->length(1)
                      ->default(0)
                      ->after('is_lowongan_disabilitas')
                      ->comment('null atau 0 lowongan umum, 1 jobfair, 2 bkk');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etam_lowongan', function (Blueprint $table) {
            if (Schema::hasColumn('etam_lowongan', 'tipe_lowongan')) {
                $table->dropColumn('tipe_lowongan');
            }
        });
    }
};
