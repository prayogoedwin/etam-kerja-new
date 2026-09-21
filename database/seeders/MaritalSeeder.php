<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MaritalSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['id' => 'B', 'name' => 'Belum Kawin'],
            ['id' => 'D', 'name' => 'Duda'],
            ['id' => 'J', 'name' => 'Janda'],
            ['id' => 'K', 'name' => 'Kawin'],
            ['id' => 'T', 'name' => '-'],
        ];

        foreach ($items as $item) {
            DB::table('etam_marital')->updateOrInsert(
                ['id' => $item['id']],
                [
                    'name' => $item['name'],
                    'updated_at' => now(),
                    'created_at' => DB::table('etam_marital')->where('id', $item['id'])->value('created_at') ?? now(),
                ]
            );
        }
    }
}
