<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAdmin;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'superadmin@etamkerja.id'],
            [
                'name' => 'Super Admin',
                'whatsapp' => '081234567890',
                'password' => 'SuperAdmin123',
                'is_finished' => 1,
                'email_verified_at' => now(),
            ]
        );

        if (! $user->hasRole($role)) {
            $user->assignRole($role);
        }

        UserAdmin::firstOrCreate(
            ['user_id' => $user->id],
            [
                'province_id' => 64,
                'jabatan' => 'Super Admin',
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]
        );
    }
}
