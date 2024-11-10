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
        $permission = Permission::firstOrCreate(['name' => 'admin-access']);
        
        // Membuat role jika belum ada
        $role = Role::firstOrCreate(['name' => 'super-admin']);

        // Menambahkan permission ke role
        $role->givePermissionTo($permission);
    }
}
