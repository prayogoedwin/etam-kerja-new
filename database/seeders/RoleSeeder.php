<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permission = Permission::firstOrCreate([
            'name' => 'role-access',
            'guard_name' => 'web',
        ]);

        $roles = [
            ['id' => 1, 'name' => 'super-admin', 'table_name' => null],
            ['id' => 3, 'name' => 'admin-provinsi', 'table_name' => null],
            ['id' => 4, 'name' => 'admin-kabkota', 'table_name' => null],
            ['id' => 5, 'name' => 'pencari-kerja', 'table_name' => 'users_pencari'],
            ['id' => 6, 'name' => 'penyedia-kerja', 'table_name' => 'users_penyedia'],
            ['id' => 7, 'name' => 'admin-bkk', 'table_name' => 'users_bkk'],
            ['id' => 8, 'name' => 'admin-kabkota-officer', 'table_name' => null],
            ['id' => 9, 'name' => 'eksekutif-provinsi', 'table_name' => null],
            ['id' => 10, 'name' => 'eksekutif-kabkota', 'table_name' => '-'],
            ['id' => 11, 'name' => 'sekretaris', 'table_name' => null],
            ['id' => 12, 'name' => 'kepala-bidang', 'table_name' => null],
            ['id' => 13, 'name' => 'kepala-sub-bidang', 'table_name' => null],
            ['id' => 14, 'name' => 'kepala-sub-bagian', 'table_name' => null],
            ['id' => 15, 'name' => 'kepala-seksi', 'table_name' => null],
            ['id' => 16, 'name' => 'kepala-balai', 'table_name' => null],
            ['id' => 17, 'name' => 'admin-bidang', 'table_name' => null],
            ['id' => 18, 'name' => 'petugas-bidang', 'table_name' => null],
            ['id' => 19, 'name' => 'admin-balai', 'table_name' => null],
            ['id' => 20, 'name' => 'petugas-balai', 'table_name' => null],
            ['id' => 21, 'name' => 'admin-blk', 'table_name' => 'users_blk'],
        ];

        foreach ($roles as $item) {
            $role = Role::firstOrCreate(
                [
                    'id' => $item['id'],
                    'name' => $item['name'],
                    'guard_name' => 'web',
                ]
            );

            $role->table_name = $item['table_name'];
            $role->save();

            if (! $role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            }
        }
    }
}
