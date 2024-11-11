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
        Schema::table('etam_lamaran', function (Blueprint $table) {
            $table->text('keterangan')->nullable()->change(); // Mengubah kolom keterangan menjadi nullable
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etam_lamaran', function (Blueprint $table) {
            $table->text('keterangan')->nullable(false)->change();
        });
    }
};
