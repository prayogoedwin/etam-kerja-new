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
        Schema::table('users_pencari', function (Blueprint $table) {
            if (! Schema::hasColumn('users_pencari', 'asal_sekolah_universitas')) {
                $table->string('asal_sekolah_universitas', 255)->after('id_jurusan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_pencari', function (Blueprint $table) {
            if (! Schema::hasColumn('users_pencari', 'asal_sekolah_universitas')) {
                $table->dropColumn('asal_sekolah_universitas');
            }
        });
    }
};
