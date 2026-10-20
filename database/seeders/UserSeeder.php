<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserAdmin;
use App\Models\UserPenyedia;
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

        $rolePenyedia = Role::firstOrCreate([
            'name' => 'penyedia-kerja',
            'guard_name' => 'web',
        ]);

        $userPenyedia = User::firstOrCreate(
            ['email' => 'penyedia@etamkerja.id'],
            [
                'name' => 'PT Demo Etam Kerja',
                'whatsapp' => '081298765432',
                'password' => 'Penyedia123',
                'is_finished' => 1,
                'email_verified_at' => now(),
            ]
        );

        if (! $userPenyedia->hasRole($rolePenyedia)) {
            $userPenyedia->assignRole($rolePenyedia);
        }

        UserPenyedia::firstOrCreate(
            ['user_id' => $userPenyedia->id],
            [
                'name' => 'PT Demo Etam Kerja',
                'luar_negri' => 0,
                'deskripsi' => 'Akun demo pemberi kerja',
                'jenis_perusahaan' => 'pt',
                'nib' => '1234567890123',
                'id_sektor' => 1,
                'id_provinsi' => 64,
                'id_kota' => 6472,
                'id_kecamatan' => 6472010,
                'alamat' => 'Jl. Demo No. 1, Samarinda',
                'kodepos' => '75111',
                'telpon' => '0541730000',
                'jabatan' => 'HRD',
                'website' => 'https://etamkerja.id',
                'status_id' => 1,
                'posted_by' => $userPenyedia->id,
            ]
        );
    }
}
