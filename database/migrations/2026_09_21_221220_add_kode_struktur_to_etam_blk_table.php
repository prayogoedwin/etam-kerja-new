<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etam_blk', function (Blueprint $table) {
            $table->string('kode_struktur', 20)->nullable()->after('kabkota_id');
        });
    }

    public function down(): void
    {
        Schema::table('etam_blk', function (Blueprint $table) {
            $table->dropColumn('kode_struktur');
        });
    }
};
