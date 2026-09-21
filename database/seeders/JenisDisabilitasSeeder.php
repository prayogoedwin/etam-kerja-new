<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JenisDisabilitasSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['id' => 1, 'nama_disabilitas' => 'Fisik'],
            ['id' => 2, 'nama_disabilitas' => 'Netra'],
            ['id' => 3, 'nama_disabilitas' => 'Rungu'],
            ['id' => 4, 'nama_disabilitas' => 'Wicara'],
            ['id' => 5, 'nama_disabilitas' => 'Rungu & Wicara'],
            ['id' => 6, 'nama_disabilitas' => 'Intelektual'],
            ['id' => 7, 'nama_disabilitas' => 'Mental'],
        ];

        foreach ($items as $item) {
            DB::table('etam_jenis_disabilitas')->updateOrInsert(
                ['id' => $item['id']],
                [
                    'nama_disabilitas' => $item['nama_disabilitas'],
                    'updated_at' => now(),
                    'created_at' => DB::table('etam_jenis_disabilitas')->where('id', $item['id'])->value('created_at') ?? now(),
                    'deleted_at' => null,
                ]
            );
        }
    }
}
