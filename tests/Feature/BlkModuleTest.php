<?php

namespace Tests\Feature;

use App\Models\BLK\EtamBlk;
use App\Models\BLK\EtamBlkPelatihan;
use App\Models\BLK\EtamBlkPelatihanPeserta;
use App\Models\User;
use App\Models\UserPencari;
use App\Models\UserPenyedia;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BlkModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super-admin', 'admin-provinsi', 'admin-kabkota', 'pencari-kerja', 'penyedia-kerja', 'admin-blk'] as $name) {
            Role::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }
    }

    public function test_super_admin_can_create_blk(): void
    {
        $admin = $this->makeUser('super-admin');

        $response = $this->actingAs($admin)->postJson(route('blk.lembaga.store'), [
            'nama_lembaga' => 'BLK Test Samarinda',
            'tipe_lembaga' => 1,
            'email' => 'blk.test@example.com',
            'whatsapp' => '081234567890',
            'alamat_lengkap' => 'Jl. Test',
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('etam_blk', [
            'nama_lembaga' => 'BLK Test Samarinda',
            'tipe_lembaga' => 1,
        ]);
    }

    public function test_admin_kabkota_cannot_crud_daftar_blk(): void
    {
        $admin = $this->makeUser('admin-kabkota');

        $this->actingAs($admin)
            ->get(route('blk.lembaga.index'))
            ->assertStatus(403);
    }

    public function test_pencari_kerja_can_register_to_pelatihan(): void
    {
        $blk = EtamBlk::create([
            'nama_lembaga' => 'BLK Pendaftaran',
            'tipe_lembaga' => 1,
            'provinsi_id' => 64,
        ]);

        $pelatihan = $this->makePelatihan($blk->id, EtamBlkPelatihan::UNTUK_PENCARI);

        $pencariUser = $this->makeUser('pencari-kerja');
        $pencari = $this->makePencari($pencariUser->id, [
            'ktp' => '6401010101010001',
            'name' => 'Pencari Test',
        ]);

        $this->actingAs($pencariUser)
            ->post(route('blk.pelatihan.daftar.store', $pelatihan->id))
            ->assertRedirect(route('blk.pelatihan.index'));

        $this->assertDatabaseHas('etam_blk_pelatihan_peserta', [
            'blk_pelatihan_id' => $pelatihan->id,
            'pencari_id' => $pencari->id,
            'status_pendaftaran' => EtamBlkPelatihanPeserta::STATUS_MENUNGGU,
        ]);
    }

    public function test_penyedia_kerja_can_register_to_pelatihan_perusahaan(): void
    {
        $blk = EtamBlk::create([
            'nama_lembaga' => 'BLK Perusahaan',
            'tipe_lembaga' => 1,
            'provinsi_id' => 64,
        ]);

        $pelatihan = $this->makePelatihan($blk->id, EtamBlkPelatihan::UNTUK_PENYEDIA);

        $penyediaUser = $this->makeUser('penyedia-kerja');
        $penyedia = $this->makePenyedia($penyediaUser->id, [
            'name' => 'PT Tes BLK',
            'nib' => '1234567890',
        ]);

        $this->actingAs($penyediaUser)
            ->post(route('blk.pelatihan.daftar.store', $pelatihan->id))
            ->assertRedirect(route('blk.pelatihan.index'));

        $this->assertDatabaseHas('etam_blk_pelatihan_peserta_perusahaan', [
            'blk_pelatihan_id' => $pelatihan->id,
            'perusahaan_id' => $penyedia->id,
            'status_pendaftaran' => EtamBlkPelatihanPeserta::STATUS_MENUNGGU,
        ]);
    }

    public function test_pencari_cannot_register_twice(): void
    {
        $blk = EtamBlk::create([
            'nama_lembaga' => 'BLK Duplikat',
            'tipe_lembaga' => 1,
            'provinsi_id' => 64,
        ]);
        $pelatihan = $this->makePelatihan($blk->id, EtamBlkPelatihan::UNTUK_PENCARI);

        $pencariUser = $this->makeUser('pencari-kerja');
        $pencari = $this->makePencari($pencariUser->id, [
            'ktp' => '6401010101010002',
            'name' => 'Pencari Duplikat',
        ]);

        EtamBlkPelatihanPeserta::create([
            'blk_pelatihan_id' => $pelatihan->id,
            'pencari_id' => $pencari->id,
            'status_pendaftaran' => 0,
            'name' => 'Pencari Duplikat',
            'created_by' => $pencariUser->id,
        ]);

        $this->actingAs($pencariUser)
            ->from(route('blk.pelatihan.daftar', $pelatihan->id))
            ->post(route('blk.pelatihan.daftar.store', $pelatihan->id))
            ->assertRedirect(route('blk.pelatihan.daftar', $pelatihan->id))
            ->assertSessionHas('error');
    }

    public function test_super_admin_can_create_user_blk(): void
    {
        $admin = $this->makeUser('super-admin');
        $blk = EtamBlk::create([
            'nama_lembaga' => 'BLK User Test',
            'tipe_lembaga' => 1,
            'provinsi_id' => 64,
        ]);

        $email = 'admin.blk.'.uniqid().'@example.com';
        $whatsapp = '08'.fake()->unique()->numerify('##########');

        $response = $this->actingAs($admin)->postJson(route('blk.users.store'), [
            'name' => 'Admin BLK Samarinda',
            'email' => $email,
            'whatsapp' => $whatsapp,
            'tipe_akun' => 3,
            'blk_id' => $blk->id,
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => 'Admin BLK Samarinda',
        ]);
        $this->assertDatabaseHas('users_blk', [
            'blk_id' => $blk->id,
            'tipe_akun' => 3,
        ]);
    }

    private function makeUser(string $role): User
    {
        $user = User::factory()->create([
            'whatsapp' => '08'.fake()->unique()->numerify('##########'),
            'is_finished' => 1,
        ]);
        $user->assignRole($role);

        return $user;
    }

    private function makePencari(int $userId, array $overrides = []): UserPencari
    {
        return UserPencari::create(array_merge([
            'user_id' => $userId,
            'ktp' => '6401010101010999',
            'name' => 'Pencari BLK',
            'tempat_lahir' => 'Samarinda',
            'tanggal_lahir' => '2000-01-01',
            'gender' => 'L',
            'id_provinsi' => 64,
            'id_kota' => 6472,
            'id_kecamatan' => 6472010,
            'alamat' => 'Jl. Pencari',
            'kodepos' => '75111',
            'id_pendidikan' => 3,
            'id_jurusan' => 1,
            'tahun_lulus' => 2018,
            'id_status_perkawinan' => '1',
            'id_agama' => 1,
            'status_id' => 1,
            'is_alumni_bkk' => 0,
            'posted_by' => $userId,
            'is_diterima' => 0,
            'ex_tambang' => 'N',
        ], $overrides));
    }

    private function makePenyedia(int $userId, array $overrides = []): UserPenyedia
    {
        return UserPenyedia::create(array_merge([
            'user_id' => $userId,
            'name' => 'PT Tes BLK',
            'nib' => '1234567890',
            'id_sektor' => 1,
            'id_provinsi' => 64,
            'id_kota' => 6472,
            'id_kecamatan' => 6472010,
            'alamat' => 'Jl. Perusahaan',
            'status_id' => 1,
            'posted_by' => $userId,
        ], $overrides));
    }

    private function makePelatihan(int $blkId, int $untuk): EtamBlkPelatihan
    {
        return EtamBlkPelatihan::create([
            'pelatihan_untuk' => $untuk,
            'blk_id' => $blkId,
            'nama_pelatihan' => 'Pelatihan Uji '.$untuk,
            'sumber_pembiayaan' => 0,
            'tanggal_pendaftaran' => now()->subDay()->toDateString(),
            'tanggal_pendaftaran_selesai' => now()->addDays(7)->toDateString(),
            'tanggal_pelaksanaan' => now()->addDays(10)->toDateString(),
            'tanggal_pelaksanaan_selesai' => now()->addDays(20)->toDateString(),
            'tipe_pelatihan' => 0,
            'status' => EtamBlkPelatihan::STATUS_AKTIF,
            'slug' => 'pelatihan-uji-'.uniqid(),
        ]);
    }
}
