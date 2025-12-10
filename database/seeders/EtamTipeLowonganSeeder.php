<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EtamTipeLowonganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('etam_tipe_lowongan')->insert([
            [
                'kode' => '0',
                'name' => 'umum',
                'keterangan' => 'lowongan umum',
                'deleted_at' => null
            ],
            [
                'kode' => '1',
                'name' => 'jobfair',
                'keterangan' => 'lowongan jobfair',
                'deleted_at' => null
            ],
            [
                'kode' => '2',
                'name' => 'bkk',
                'keterangan' => 'lowongan bkk',
                'deleted_at' => null
            ],
            [
                'kode' => '3',
                'name' => 'magang mandiri',
                'keterangan' => 'lowongan magang mandiri',
                'deleted_at' => null
            ],
            [
                'kode' => '4',
                'name' => 'magang pemerintah',
                'keterangan' => 'lowongan magang pemerintah',
                'deleted_at' => null
            ],
        ]);
    }
}
