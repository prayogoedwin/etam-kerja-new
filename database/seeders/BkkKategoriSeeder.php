<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BkkKategoriSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['id' => 3, 'name' => 'Lembaga Pelatihan Kerja'],
            ['id' => 11, 'name' => 'Sekolah Pendidikan Menengah Umum (SMU)'],
            ['id' => 12, 'name' => 'Sekolah Pendidikan Menengah Kejuruan (SMK)'],
            ['id' => 21, 'name' => 'Sekolah Pendidikan Tinggi (FAK. EKSAK)'],
            ['id' => 22, 'name' => 'Sekolah Pendidikan Tinggi (FAK. NON EKSAK)'],
        ];

        foreach ($items as $item) {
            DB::table('etam_bkk_kategori')->updateOrInsert(
                ['id' => $item['id']],
                [
                    'name' => $item['name'],
                    'updated_at' => now(),
                    'created_at' => DB::table('etam_bkk_kategori')->where('id', $item['id'])->value('created_at') ?? now(),
                    'deleted_at' => null,
                ]
            );
        }
    }
}
