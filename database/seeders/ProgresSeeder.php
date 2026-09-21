<?php

namespace Database\Seeders;

use App\Models\Progress;
use Illuminate\Database\Seeder;

class ProgresSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['id' => 1, 'kode' => 1, 'name' => 'Diperiksa', 'modul' => 'lamaran'],
            ['id' => 2, 'kode' => 2, 'name' => 'Panggilan', 'modul' => 'lamaran'],
            ['id' => 3, 'kode' => 3, 'name' => 'Diterima', 'modul' => 'lamaran'],
            ['id' => 4, 'kode' => 4, 'name' => 'Belum Ditanggapi', 'modul' => 'lamaran'],
            ['id' => 5, 'kode' => 5, 'name' => 'Tidak Sesuai Kriteria', 'modul' => 'lamaran'],
            ['id' => 7, 'kode' => 0, 'name' => 'Menunggu', 'modul' => 'lowongan'],
            ['id' => 8, 'kode' => 1, 'name' => 'Acc', 'modul' => 'lowongan'],
            ['id' => 9, 'kode' => 2, 'name' => 'Ditolak', 'modul' => 'lowongan'],
            ['id' => 10, 'kode' => 3, 'name' => 'Ditutup', 'modul' => 'lowongan'],
        ];

        foreach ($items as $item) {
            Progress::updateOrCreate(
                ['id' => $item['id']],
                [
                    'kode' => $item['kode'],
                    'name' => $item['name'],
                    'modul' => $item['modul'],
                ]
            );
        }
    }
}
