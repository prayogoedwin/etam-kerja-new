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
        Schema::table('user_admins', function (Blueprint $table) {
            $table->string('foto', 255)->nullable(); // Menambahkan kolom 'foto' dengan tipe data VARCHAR(255)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_admins', function (Blueprint $table) {
            $table->dropColumn('foto');
        });
    }
};
