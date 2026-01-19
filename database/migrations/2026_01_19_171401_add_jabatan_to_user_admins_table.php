<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_admins', function (Blueprint $table) {
            // $table->string('jabatan')->nullable()->after('kecamatan_id');
            $table->string('jabatan')->nullable()->default('PETUGAS ANTAR KERJA')->after('kecamatan_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_admins', function (Blueprint $table) {
            $table->dropColumn('jabatan');
        });
    }
};