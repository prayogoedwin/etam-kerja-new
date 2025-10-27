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
            if (! Schema::hasColumn('etam_lowongan', 'kisaran_gaji_akhir')) {
                $table->integer('kisaran_gaji_akhir')->nullable()->after('kisaran_gaji');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etam_lowongan', function (Blueprint $table) {
            if (! Schema::hasColumn('etam_lowongan', 'kisaran_gaji_akhir')) {
                $table->dropColumn('kisaran_gaji_akhir');
            }
        });
    }
};
