<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etam_pjk3_suket', function (Blueprint $table) {
            $table->json('lhpp')->nullable()->after('rekomendasi');
        });
    }

    public function down(): void
    {
        Schema::table('etam_pjk3_suket', function (Blueprint $table) {
            $table->dropColumn('lhpp');
        });
    }
};
