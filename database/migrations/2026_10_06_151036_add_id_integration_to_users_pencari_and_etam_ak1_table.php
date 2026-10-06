<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users_pencari', function (Blueprint $table) {
            $table->unsignedBigInteger('id_integration')->nullable()->after('posted_by');
            $table->index('id_integration');
        });

        Schema::table('etam_ak1', function (Blueprint $table) {
            $table->unsignedBigInteger('id_integration')->nullable()->after('dicetak_oleh');
            $table->index('id_integration');
        });
    }

    public function down(): void
    {
        Schema::table('users_pencari', function (Blueprint $table) {
            $table->dropIndex(['id_integration']);
            $table->dropColumn('id_integration');
        });

        Schema::table('etam_ak1', function (Blueprint $table) {
            $table->dropIndex(['id_integration']);
            $table->dropColumn('id_integration');
        });
    }
};
