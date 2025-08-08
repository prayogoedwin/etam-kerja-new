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
        Schema::create('etam_desa', function (Blueprint $table) {
            $table->char('id', 7)->primary();
            $table->char('district_id', 7);  // Foreign key ke tabel etam_kecamatan
            $table->string('name', 255);

            // Menambahkan foreign key constraint
            $table->foreign('district_id')->references('id')->on('etam_kecamatan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etam_desa', function (Blueprint $table) {
            // Hapus foreign key jika ada
            $table->dropForeign(['district_id']);
        });

        Schema::dropIfExists('etam_desa');
    }
};
