<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etam_tte', function (Blueprint $table) {
            $table->string('token_verifikasi', 64)->nullable()->unique()->after('path_file_after');
        });
    }

    public function down(): void
    {
        Schema::table('etam_tte', function (Blueprint $table) {
            $table->dropUnique(['token_verifikasi']);
            $table->dropColumn('token_verifikasi');
        });
    }
};
