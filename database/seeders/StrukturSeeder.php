<?php

namespace Database\Seeders;

use App\Models\EtamStruktur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StrukturSeeder extends Seeder
{
    public function run(): void
    {
        $tipe = 1;
        $kodeLokasi = '64';

        $items = [
            ['kode_bidang' => '1', 'nama' => 'Sekretariat'],
            ['kode_bidang' => '11', 'nama' => 'Sub Bagian Umum'],
            ['kode_bidang' => '12', 'nama' => 'Sub Bagian Keuangan'],

            ['kode_bidang' => '2', 'nama' => 'Bidang Pembinaan dan Pengawasan'],

            ['kode_bidang' => '3', 'nama' => 'Bidang Pengembangan Tenaga Kerja'],
            ['kode_bidang' => '31', 'nama' => 'Seksi Bina Tenaga Kerja dan Perluasan Tenaga Kerja'],
            ['kode_bidang' => '32', 'nama' => 'Seksi Bina Informasi Bursa Kerja dan Penempatan Tenaga Kerja'],
            ['kode_bidang' => '33', 'nama' => 'Seksi Bina Pelatihan dan Produktivitas Tenaga Kerja'],
            ['kode_bidang' => '34', 'nama' => 'UPTD. Balai Latihan Kerja Industri Balikpapan'],
            ['kode_bidang' => '35', 'nama' => 'UPTD. Balai Latihan Kerja Industri Bontang'],

            ['kode_bidang' => '4', 'nama' => 'Bidang Hubungan Industrial dan Jamsostek'],
            ['kode_bidang' => '41', 'nama' => 'Seksi Syarat Kerja PP & PKB'],
            ['kode_bidang' => '42', 'nama' => 'Seksi Jaminan Sosial & Pengupahan'],

            ['kode_bidang' => '5', 'nama' => 'Bidang Transmigrasi'],
            ['kode_bidang' => '51', 'nama' => 'Seksi Perencanaan & Pengembangan Kawasan Transmigrasi'],
            ['kode_bidang' => '52', 'nama' => 'Seksi Penyiapan Permukiman & Fasilitasi Perpindahan Transmigrasi'],
            ['kode_bidang' => '53', 'nama' => 'Seksi Pembinaan Pemberdayaan dan Pelayanan Masyarakat Trans'],
        ];

        $kodeBidang = [];

        foreach ($items as $item) {
            $kodeBidang[] = $item['kode_bidang'];

            EtamStruktur::updateOrCreate(
                [
                    'tipe' => $tipe,
                    'kode_lokasi' => $kodeLokasi,
                    'kode_bidang' => $item['kode_bidang'],
                ],
                [
                    'nama' => $item['nama'],
                    'slug' => Str::slug($item['nama']),
                ]
            );
        }

        EtamStruktur::query()
            ->where('tipe', $tipe)
            ->where('kode_lokasi', $kodeLokasi)
            ->whereNotIn('kode_bidang', $kodeBidang)
            ->delete();
    }
}
