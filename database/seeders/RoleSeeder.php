<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Membuat permission jika belum ada
        $permission = Permission::firstOrCreate(['name' => 'role-access']);
        
        // Membuat role jika belum ada dan memberi permission ke role
        $roles = [
            'super-admin',
            'admin-provinsi',
            'admin-kabkota',
            'pencari-kerja',
            'penyedia-kerja',
            'admin-bkk',
            'admin-kabkota-officer',
        ];

        foreach ($roles as $roleName) {
            // Membuat role baru jika belum ada
            $role = Role::firstOrCreate(['name' => $roleName]);

            // Menambahkan permission ke role (sesuaikan permission jika perlu)
            $role->givePermissionTo($permission);
        }
    }
}

