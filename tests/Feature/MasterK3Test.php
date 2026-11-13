<?php

namespace Tests\Feature;

use App\Models\Pengawasan\K3\EtamPengawasanK3Ajuan;
use App\Models\Pengawasan\K3\EtamPengawasanK3Jenis;
use App\Models\Pengawasan\K3\EtamPengawasanK3Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MasterK3Test extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'super-admin', 'admin-provinsi', 'admin-bidang', 'petugas-bidang',
            'kepala-bidang', 'kepala-seksi', 'penyedia-kerja', 'pencari-kerja',
            'eksekutif-provinsi',
        ] as $name) {
            Role::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }
    }

    public function test_super_admin_bisa_crud_kategori_dan_jenis(): void
    {
        $admin = $this->makeUser('super-admin');
        $kode = 'Z'.strtoupper(substr(uniqid(), -4));

        $this->actingAs($admin)
            ->get(route('pengawasan.k3.master.kategori.index'))
            ->assertOk()
            ->assertSee('Kategori K3')
            ->assertSee('Tambah kategori');

        $this->actingAs($admin)
            ->post(route('pengawasan.k3.master.kategori.store'), [
                'nama' => 'Kategori Uji '.$kode,
                'kode' => strtolower($kode), // controller menyimpan huruf besar
                'keterangan' => 'Master uji',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $kategori = EtamPengawasanK3Kategori::query()->where('kode', $kode)->first();
        $this->assertNotNull($kategori);
        $this->assertSame($kode, $kategori->kode);

        $this->actingAs($admin)
            ->put(route('pengawasan.k3.master.kategori.update', $kategori->id), [
                'nama' => 'Kategori Uji Revisi',
                'kode' => $kode,
                'keterangan' => 'Direvisi',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame('Kategori Uji Revisi', $kategori->fresh()->nama);

        $this->actingAs($admin)
            ->post(route('pengawasan.k3.master.jenis.store'), [
                'kategori_id' => $kategori->id,
                'nama' => 'Alat Uji '.$kode,
                'keterangan' => 'Jenis uji',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $jenis = EtamPengawasanK3Jenis::query()->where('nama', 'Alat Uji '.$kode)->first();
        $this->assertNotNull($jenis);

        $this->actingAs($admin)
            ->put(route('pengawasan.k3.master.jenis.update', $jenis->id), [
                'kategori_id' => $kategori->id,
                'nama' => 'Alat Uji Revisi',
                'keterangan' => 'Direvisi',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->actingAs($admin)
            ->delete(route('pengawasan.k3.master.jenis.destroy', $jenis->id))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('etam_pengawasan_k3_jenis', ['id' => $jenis->id]);

        $this->actingAs($admin)
            ->delete(route('pengawasan.k3.master.kategori.destroy', $kategori->id))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSoftDeleted('etam_pengawasan_k3_kategori', ['id' => $kategori->id]);
    }

    public function test_admin_bidang_pembinaan_bisa_buka_dashboard(): void
    {
        $admin = $this->makeUser('admin-bidang', ['kode_struktur' => '2']);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Bidang Pembinaan dan Pengawasan')
            ->assertSee('Layanan K3');
    }

    public function test_admin_provinsi_dan_admin_bidang_k3_bisa_akses_master(): void
    {
        $prov = $this->makeUser('admin-provinsi');
        $bidang = $this->makeUser('admin-bidang', ['kode_struktur' => '21']);

        $this->actingAs($prov)
            ->get(route('pengawasan.k3.master.jenis.index'))
            ->assertOk()
            ->assertSee('Jenis objek K3');

        $this->actingAs($bidang)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('pengawasan.k3.master.kategori.index'))
            ->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_admin_bidang_bukan_k3_dan_pengawas_ditolak(): void
    {
        $hi = $this->makeUser('admin-bidang', ['kode_struktur' => '41']);
        $pengawas = $this->makeUser('petugas-bidang', ['kode_struktur' => '21']);

        $this->actingAs($hi)
            ->get(route('pengawasan.k3.master.kategori.index'))
            ->assertForbidden();

        $this->actingAs($pengawas)
            ->get(route('pengawasan.k3.master.jenis.index'))
            ->assertForbidden();

        $this->actingAs($pengawas)
            ->post(route('pengawasan.k3.master.kategori.store'), [
                'nama' => 'Tidak boleh',
            ])
            ->assertForbidden();
    }

    public function test_menu_master_hanya_untuk_akun_yang_boleh(): void
    {
        $super = $this->makeUser('super-admin');
        $bidang = $this->makeUser('admin-bidang', ['kode_struktur' => '21']);
        $pengawas = $this->makeUser('petugas-bidang', ['kode_struktur' => '21']);
        $hi = $this->makeUser('admin-bidang', ['kode_struktur' => '41']);

        $this->actingAs($super)
            ->get(route('pengawasan.suket.admin.pengajuan'))
            ->assertOk()
            ->assertSee('Kategori K3')
            ->assertSee('Jenis objek K3');

        $this->actingAs($bidang)
            ->get(route('pengawasan.suket.admin.pengajuan'))
            ->assertOk()
            ->assertSee('Kategori K3')
            ->assertSee('Jenis objek K3');

        $this->actingAs($pengawas)
            ->get(route('pengawasan.suket.admin.pengajuan'))
            ->assertOk()
            ->assertDontSee('Jenis objek K3');

        $this->actingAs($hi)
            ->get(route('pengawasan.suket.admin.pengajuan'))
            ->assertForbidden();
    }

    public function test_kategori_tidak_bisa_dihapus_jika_masih_punya_jenis(): void
    {
        $admin = $this->makeUser('super-admin');
        $kategori = EtamPengawasanK3Kategori::create([
            'nama' => 'Kategori Terpakai',
            'kode' => 'Y'.substr(uniqid(), -4),
        ]);
        EtamPengawasanK3Jenis::create([
            'kategori_id' => $kategori->id,
            'nama' => 'Jenis Terpakai',
        ]);

        $this->actingAs($admin)
            ->delete(route('pengawasan.k3.master.kategori.destroy', $kategori->id))
            ->assertStatus(422)
            ->assertJson(['success' => false]);

        $this->assertDatabaseHas('etam_pengawasan_k3_kategori', [
            'id' => $kategori->id,
            'deleted_at' => null,
        ]);
    }

    public function test_jenis_tidak_bisa_dihapus_jika_masih_dipakai_ajuan(): void
    {
        $admin = $this->makeUser('super-admin');
        $kategori = EtamPengawasanK3Kategori::create([
            'nama' => 'Kategori Ajuan',
            'kode' => 'X'.substr(uniqid(), -4),
        ]);
        $jenis = EtamPengawasanK3Jenis::create([
            'kategori_id' => $kategori->id,
            'nama' => 'Jenis Ajuan',
        ]);
        EtamPengawasanK3Ajuan::create([
            'penyedia_id' => $admin->id,
            'kategori_id' => $kategori->id,
            'jenis_id' => $jenis->id,
            'nama_alat' => 'Alat',
            'lokasi_alat' => 'Lokasi',
            'kapasitas_alat' => '1',
            'jumlah_unit' => 1,
        ]);

        $this->actingAs($admin)
            ->delete(route('pengawasan.k3.master.jenis.destroy', $jenis->id))
            ->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_kode_kategori_tidak_boleh_dobel(): void
    {
        $admin = $this->makeUser('super-admin');
        $kode = 'W'.substr(uniqid(), -4);
        EtamPengawasanK3Kategori::create([
            'nama' => 'Sudah ada',
            'kode' => $kode,
        ]);

        $this->actingAs($admin)
            ->post(route('pengawasan.k3.master.kategori.store'), [
                'nama' => 'Kategori lain',
                'kode' => $kode,
            ])
            ->assertOk()
            ->assertJson(['success' => false]);
    }

    private function makeUser(string $role, array $overrides = []): User
    {
        $user = User::factory()->create(array_merge([
            'whatsapp' => '08'.fake()->unique()->numerify('##########'),
            'is_finished' => 1,
        ], $overrides));
        $user->assignRole($role);

        return $user;
    }
}
