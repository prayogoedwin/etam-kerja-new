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
        Schema::create('etam_tipe_lowongan', function (Blueprint $table) {
            $table->string('kode', 20)->primary();   // kolom utama tanpa auto increment
            $table->string('name', 100);
            $table->string('keterangan', 255)->nullable();
            $table->timestamp('deleted_at')->nullable();
        });

        // Jalankan seeder langsung saat migrasi
        \Artisan::call('db:seed', [
            '--class' => 'Database\\Seeders\\EtamTipeLowonganSeeder'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_tipe_lowongan');
    }
};
