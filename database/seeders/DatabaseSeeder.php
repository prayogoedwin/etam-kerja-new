<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            StrukturSeeder::class,
            PengawasanK3KategoriSeeder::class,
            PengawasanK3JenisSeeder::class,
            BlkSeeder::class,
            AgamaSeeder::class,
            ProgresSeeder::class,
            JenisDisabilitasSeeder::class,
            BkkKategoriSeeder::class,
            MaritalSeeder::class,
        ]);
    }
}
