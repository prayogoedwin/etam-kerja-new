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
        Schema::create('etam_kecamatan', function (Blueprint $table) {
            $table->char('id', 7)->primary();
            $table->char('regency_id', 4);
            $table->string('name', 255);

            // Menambahkan foreign key constraint
            $table->foreign('regency_id')->references('id')->on('etam_kabkota')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('etam_kecamatan', function (Blueprint $table) {
            // Hapus foreign key jika ada
            $table->dropForeign(['regency_id']);
        });

        Schema::dropIfExists('etam_kecamatan');
    }
};
