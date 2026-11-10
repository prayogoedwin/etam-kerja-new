<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etam_hi_pp_ajuan', function (Blueprint $table) {
            $table->unsignedTinyInteger('status_ttd')->default(0)->after('dok_produk_akhir');
        });
    }

    public function down(): void
    {
        Schema::table('etam_hi_pp_ajuan', function (Blueprint $table) {
            $table->dropColumn('status_ttd');
        });
    }
};
