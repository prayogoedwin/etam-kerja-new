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
            $table->string('dok_final_konsep_pp', 255)
                  ->nullable()
                  ->after('dok_produk_akhir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etam_hi_pp_ajuan', function (Blueprint $table) {
            $table->dropColumn('dok_final_konsep_pp');
        });
    }
};
