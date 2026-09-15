<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
            'super-admin',
            'admin-provinsi',
            'admin-kabkota',
            'pencari-kerja',
            'penyedia-kerja',
            'admin-bkk',
            'admin-kabkota-officer',
            'sekretaris',
            'kepala-bidang',
            'kepala-sub-bidang',
            'kepala-sub-bagian',
            'kepala-seksi',
            'kepala-balai',
            'admin-bidang',
            'petugas-bidang',
        ];

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

            if (! $role->hasPermissionTo($permission)) {
                $role->givePermissionTo($permission);
            }
        }
    }
}
