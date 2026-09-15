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
        Schema::create('etam_hi_pp_syaratdokumen', function (Blueprint $table) {
            $table->increments('id'); // INT AUTO_INCREMENT PRIMARY KEY
            $table->string('nama', 255);
            $table->string('keterangan', 255)->nullable();
        });

        // Insert 11 row langsung
        DB::table('etam_hi_pp_syaratdokumen')->insert([
            ['nama' => 'Surat Permohonan dari perusahaan', 'keterangan' => null],
            ['nama' => 'Surat Keterangan BPJS Kesehatan dan Ketenagakerjaan', 'keterangan' => null],
            ['nama' => 'Bukti Terakhir Pembayaran BPJS', 'keterangan' => null],
            ['nama' => 'Surat Pernyataan telah melampirkan Struktur Skala Upah (SUSU)', 'keterangan' => null],
            ['nama' => 'Surat Pernyataan ada / tidak ada Serikat Pekerja / Buruh', 'keterangan' => null],
            ['nama' => 'Surat Pernyataan Lokasi Perusahaan berada di beberapa tempat / cabang', 'keterangan' => null],
            ['nama' => 'SK Lama / Peraturan Perusahaan Lama (khusus untuk perpanjangan)', 'keterangan' => null],
            ['nama' => 'Konsep Peraturan Perusahaan sebanyak 3 rangkap (tiap halaman wajib diparaf oleh manajemen perusahaan)', 'keterangan' => null],
            ['nama' => 'Surat Pernyataan masuk keanggotaan APINDO', 'keterangan' => null],
            ['nama' => 'Bukti Data Lapor WLKP Online', 'keterangan' => null],
            ['nama' => 'Bukti pencatatan LKS Bipartit / Surat Pernyataan belum membuat LKS Bipartit', 'keterangan' => null],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_hi_pp_syaratdokumen');
    }
};
