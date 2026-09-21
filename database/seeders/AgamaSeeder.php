<?php

namespace Database\Seeders;

use App\Models\Agama;
use Illuminate\Database\Seeder;

class AgamaSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['id' => 1, 'name' => 'Islam'],
            ['id' => 2, 'name' => 'Katholik'],
            ['id' => 3, 'name' => 'Protestan'],
            ['id' => 4, 'name' => 'Hindhu'],
            ['id' => 5, 'name' => 'Budha'],
            ['id' => 6, 'name' => 'Lainnya'],
        ];

        foreach ($items as $item) {
            Agama::updateOrCreate(
                ['id' => $item['id']],
                [
                    'name' => $item['name'],
                    'keterangan' => null,
                ]
            );
        }
    }
}
