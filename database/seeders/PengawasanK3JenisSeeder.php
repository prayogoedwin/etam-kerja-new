<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PengawasanK3JenisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // Kategori 1: Listrik
            ['kategori_id' => 1, 'nama' => 'Escalator (travelator)', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 1, 'nama' => 'Elevator (lift)', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 1, 'nama' => 'Instalasi Penyalur Petir', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],

            // Kategori 2: Pesawat Tenaga dan Produksi
            ['kategori_id' => 2, 'nama' => 'Penggerak mula Motor diesel dan sejenisnya', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 2, 'nama' => 'Penggerak mula Turbin uap dan sejenisnya', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 2, 'nama' => 'Penggerak mula Motor bensin dan sejenisnya', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 2, 'nama' => 'Mesin perkakas dan sejenisnya', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 2, 'nama' => 'Mesin produksi dan sejenisnya', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 2, 'nama' => 'Tanur dan sejenisnya', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],

            // Kategori 3: Pesawat Uap, Bejana Tekanan, dan Tangki Timbun
            ['kategori_id' => 3, 'nama' => 'Akta Izin Pesawat Uap (Surat Keterangan Pesawat Uap)', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 3, 'nama' => 'Bejana tekanan', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 3, 'nama' => 'Tangki timbun', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],

            // Kategori 4: Kebakaran
            ['kategori_id' => 4, 'nama' => 'apar', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 4, 'nama' => 'alarm kebakaran', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 4, 'nama' => 'Sprinkler', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 4, 'nama' => 'instalasi khusus', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 4, 'nama' => 'hydrant', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],

            // Kategori 5: Pesawat Angkat dan Pesawat Angkut
            ['kategori_id' => 5, 'nama' => 'Overhead Travelling Crane dan sejenisnya', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 5, 'nama' => 'Kran menara (Tower Crane)', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 5, 'nama' => 'Kran kelabang (Crawler Crane)', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 5, 'nama' => 'Gondola', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 5, 'nama' => 'Forklift', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 5, 'nama' => 'Konveyor dan sejenisnya', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 5, 'nama' => 'Excavator dan sejenisnya', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 5, 'nama' => 'Gantry Crane dan sejenisnya', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 5, 'nama' => 'Grader dan sejenisnya', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 5, 'nama' => 'Loader dan sejenisnya', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],

            // Kategori 6: Lingkungan Kerja
            ['kategori_id' => 6, 'nama' => 'Faktor fisika/kimia/biologi/ergonomi/psikologi di tempat kerja', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
            ['kategori_id' => 6, 'nama' => 'Penerapan Higiene dan Sanitasi di tempat kerja', 'keterangan' => null, 'created_at' => '2026-10-02 02:02:02', 'updated_at' => now(), 'deleted_at' => null],
        ];

        DB::table('etam_pengawasan_k3_jenis')->insert($data);
    }
}
