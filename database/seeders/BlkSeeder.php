<?php

namespace Database\Seeders;

use App\Models\BLK\EtamBlk;
use App\Models\EtamStruktur;
use App\Models\Kabkota;
use Illuminate\Database\Seeder;

class BlkSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'kode_struktur' => '34',
                'nama_lembaga' => 'UPTD. Balai Latihan Kerja Industri Balikpapan',
                'kabkota_nama' => 'Balikpapan',
            ],
            [
                'kode_struktur' => '35',
                'nama_lembaga' => 'UPTD. Balai Latihan Kerja Industri Bontang',
                'kabkota_nama' => 'Bontang',
            ],
        ];

        foreach ($items as $item) {
            $struktur = EtamStruktur::query()
                ->where('kode_bidang', $item['kode_struktur'])
                ->first();

            EtamBlk::updateOrCreate(
                ['kode_struktur' => $item['kode_struktur']],
                [
                    'tipe_lembaga' => EtamBlk::TIPE_PROVINSI,
                    'nama_lembaga' => $struktur->nama ?? $item['nama_lembaga'],
                    'provinsi_id' => 64,
                    'kabkota_id' => $this->kabkotaIdByName($item['kabkota_nama']),
                ]
            );
        }
    }

    private function kabkotaIdByName(string $name): ?int
    {
        $kabkota = Kabkota::query()
            ->where('name', 'like', '%'.$name.'%')
            ->first();

        return $kabkota?->id;
    }
}
