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
        Schema::create('etam_hi_pp_jenisajuan', function (Blueprint $table) {
            $table->increments('id'); // int(11) AUTO_INCREMENT PRIMARY KEY
            $table->string('nama', 255);
            $table->string('keterangan', 255)->nullable();
        });

        // Insert 4 row langsung
        DB::table('etam_hi_pp_jenisajuan')->insert([
            ['nama' => 'Perpanjangan',           'keterangan' => null],
            ['nama' => 'Baru',                   'keterangan' => null],
            ['nama' => 'Perpanjangan 1 Tahun',   'keterangan' => null],
            ['nama' => 'Perubahan',              'keterangan' => null],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etam_hi_pp_jenisajuan');
    }
};
