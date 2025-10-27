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
            if (! Schema::hasColumn('users_pencari', 'nilai_ijazah_ipk')) {
                $table->decimal('nilai_ijazah_ipk', 5, 2)->nullable()->after('asal_sekolah_universitas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_pencari', function (Blueprint $table) {
            if (! Schema::hasColumn('users_pencari', 'nilai_ijazah_ipk')) {
                $table->dropColumn('nilai_ijazah_ipk');
            }
        });
    }
};
