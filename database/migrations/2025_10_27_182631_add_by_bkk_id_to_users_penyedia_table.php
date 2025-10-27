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
        Schema::table('users_penyedia', function (Blueprint $table) {
            if (! Schema::hasColumn('users_penyedia', 'by_bkk_id')) {
                $table->integer('by_bkk_id')->after('foto')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_penyedia', function (Blueprint $table) {
            if (! Schema::hasColumn('users_penyedia', 'by_bkk_id')) {
                $table->dropColumn('by_bkk_id');
            }
        });
    }
};
