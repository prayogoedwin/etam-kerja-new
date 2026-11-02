<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etam_pengawasan_k3_kategori', function (Blueprint $table) {
            $table->string('kode', 20)->nullable()->after('nama');
        });

        $kode = [
            1 => 'LIS',
            2 => 'PTP',
            3 => 'PUBT',
            4 => 'KBK',
            5 => 'PAPA',
            6 => 'LK',
        ];

        foreach ($kode as $id => $value) {
            DB::table('etam_pengawasan_k3_kategori')->where('id', $id)->update(['kode' => $value]);
        }
    }

    public function down(): void
    {
        Schema::table('etam_pengawasan_k3_kategori', function (Blueprint $table) {
            $table->dropColumn('kode');
        });
    }
};
