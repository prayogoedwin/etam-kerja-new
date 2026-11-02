<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengawasanK3KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'nama' => 'Listrik',
                'kode' => 'LIS',
                'keterangan' => null,
                'created_at' => '2026-10-02 02:02:02',
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'nama' => 'Pesawat Tenaga dan Produksi',
                'kode' => 'PTP',
                'keterangan' => null,
                'created_at' => '2026-10-02 02:02:02',
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'id' => 3,
                'nama' => 'Pesawat Uap, Bejana Tekanan, dan Tangki Timbun',
                'kode' => 'PUBT',
                'keterangan' => null,
                'created_at' => '2026-10-02 02:02:02',
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'id' => 4,
                'nama' => 'Kebakaran',
                'kode' => 'KBK',
                'keterangan' => null,
                'created_at' => '2026-10-02 02:02:02',
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'id' => 5,
                'nama' => 'Pesawat Angkat dan Pesawat Angkut',
                'kode' => 'PAPA',
                'keterangan' => null,
                'created_at' => '2026-10-02 02:02:02',
                'updated_at' => now(),
                'deleted_at' => null,
            ],
            [
                'id' => 6,
                'nama' => 'Lingkungan Kerja',
                'kode' => 'LK',
                'keterangan' => null,
                'created_at' => '2026-10-02 02:02:02',
                'updated_at' => now(),
                'deleted_at' => null,
            ],
        ];

        DB::table('etam_pengawasan_k3_kategori')->insert($data);
    }
}
