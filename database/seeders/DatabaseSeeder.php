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
            BlkSeeder::class,
            AgamaSeeder::class,
            ProgresSeeder::class,
            JenisDisabilitasSeeder::class,
            BkkKategoriSeeder::class,
            MaritalSeeder::class,
        ]);
    }
}
