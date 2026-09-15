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
            ['name' => 'super-admin', 'table_name' => null],
            ['name' => 'admin-provinsi', 'table_name' => null],
            ['name' => 'admin-kabkota', 'table_name' => null],
            ['name' => 'pencari-kerja', 'table_name' => 'users_pencari'],
            ['name' => 'penyedia-kerja', 'table_name' => 'users_penyedia'],
            ['name' => 'admin-bkk', 'table_name' => 'users_bkk'],
            ['name' => 'admin-kabkota-officer', 'table_name' => null],
            ['name' => 'eksekutif-provinsi', 'table_name' => null],
            ['name' => 'eksekutif-kabkota', 'table_name' => '-'],
            ['name' => 'sekretaris', 'table_name' => null],
            ['name' => 'kepala-bidang', 'table_name' => null],
            ['name' => 'kepala-sub-bidang', 'table_name' => null],
            ['name' => 'kepala-sub-bagian', 'table_name' => null],
            ['name' => 'kepala-seksi', 'table_name' => null],
            ['name' => 'kepala-balai', 'table_name' => null],
            ['name' => 'admin-bidang', 'table_name' => null],
            ['name' => 'petugas-bidang', 'table_name' => null],
        ];

        foreach ($roles as $item) {
            $role = Role::firstOrCreate(
                [
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
