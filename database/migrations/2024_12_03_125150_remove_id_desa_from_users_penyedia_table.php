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
            $table->dropColumn('id_desa'); // Menghapus kolom id_desa
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_penyedia', function (Blueprint $table) {
            $table->unsignedBigInteger('id_desa')->nullable(); // Mengembalikan kolom id_desa
        });
    }
};
