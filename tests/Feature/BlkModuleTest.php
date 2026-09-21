<?php

namespace Tests\Feature;

use App\Models\BLK\EtamBlk;
use App\Models\BLK\EtamBlkForm;
use App\Models\BLK\EtamBlkFormPertanyaan;
use App\Models\BLK\EtamBlkPelatihan;
use App\Models\BLK\EtamBlkPelatihanJawaban;
use App\Models\BLK\EtamBlkPelatihanPeserta;
use App\Models\Lamaran;
use App\Models\Lowongan;
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

        foreach (['super-admin', 'admin-provinsi', 'admin-kabkota', 'pencari-kerja', 'penyedia-kerja', 'admin-blk', 'kepala-balai', 'admin-balai', 'petugas-balai'] as $name) {
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

    public function test_pencari_can_see_history_pelatihan_kerja(): void
    {
        $blk = EtamBlk::create([
            'nama_lembaga' => 'BLK History Pencari',
            'tipe_lembaga' => 1,
            'provinsi_id' => 64,
        ]);
        $pelatihan = $this->makePelatihan($blk->id, EtamBlkPelatihan::UNTUK_PENCARI);
        $pelatihan->update(['nama_pelatihan' => 'Pelatihan Las Listrik']);

        $pencariUser = $this->makeUser('pencari-kerja');
        $pencari = $this->makePencari($pencariUser->id, [
            'ktp' => '6401010101010777',
            'name' => 'Pencari History',
        ]);
        $orangLain = $this->makeUser('pencari-kerja');
        $pencariLain = $this->makePencari($orangLain->id, [
            'ktp' => '6401010101010778',
            'name' => 'Pencari Lain',
        ]);

        EtamBlkPelatihanPeserta::create([
            'blk_pelatihan_id' => $pelatihan->id,
            'pencari_id' => $pencari->id,
            'status_pendaftaran' => EtamBlkPelatihanPeserta::STATUS_SELEKSI,
            'name' => 'Pencari History',
            'created_by' => $pencariUser->id,
        ]);
        EtamBlkPelatihanPeserta::create([
            'blk_pelatihan_id' => $pelatihan->id,
            'pencari_id' => $pencariLain->id,
            'status_pendaftaran' => EtamBlkPelatihanPeserta::STATUS_DITERIMA,
            'name' => 'Pencari Lain',
            'created_by' => $orangLain->id,
        ]);

        $this->actingAs($pencariUser)
            ->get(route('historypelatihan.pencari.index'))
            ->assertOk()
            ->assertSee('History Pelatihan Kerja');

        $response = $this->actingAs($pencariUser)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('historypelatihan.pencari.index'));

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('nama_pelatihan');
        $this->assertTrue($names->contains('Pelatihan Las Listrik'));
        $this->assertSame(1, $names->count());
        $this->assertStringContainsString('Proses Seleksi', collect($response->json('data'))->pluck('status')->first());
        $this->assertStringNotContainsString('Diterima', collect($response->json('data'))->pluck('status')->implode(' '));
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

        $list = $this->actingAs($pencariUser)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('blk.pelatihan.index'));
        $list->assertOk();
        $list->assertSee('Menunggu', false);
        $list->assertSee('Lihat Status', false);
        $list->assertDontSee('>Daftar</a>', false);
    }

    public function test_admin_can_view_peserta_profile_and_pelatihan_history(): void
    {
        $blk = EtamBlk::create([
            'nama_lembaga' => 'BLK Profil',
            'tipe_lembaga' => 1,
            'provinsi_id' => 64,
            'kode_struktur' => '34',
        ]);
        $pelatihanA = $this->makePelatihan($blk->id, EtamBlkPelatihan::UNTUK_PENCARI);
        $pelatihanA->update(['nama_pelatihan' => 'Pelatihan Las']);
        $pelatihanB = $this->makePelatihan($blk->id, EtamBlkPelatihan::UNTUK_PENCARI);
        $pelatihanB->update(['nama_pelatihan' => 'Pelatihan Operator']);

        $admin = $this->makeUser('admin-balai', [
            'kode_struktur' => '34',
            'lokasi_kerja' => '64',
        ]);
        $pencariUser = $this->makeUser('pencari-kerja');
        $pencari = $this->makePencari($pencariUser->id, [
            'ktp' => '6401010101010888',
            'name' => 'Peserta Riwayat',
        ]);

        EtamBlkPelatihanPeserta::create([
            'blk_pelatihan_id' => $pelatihanA->id,
            'pencari_id' => $pencari->id,
            'status_pendaftaran' => 0,
            'name' => 'Peserta Riwayat',
            'ktp' => '6401010101010888',
            'created_by' => $pencariUser->id,
        ]);
        $peserta = EtamBlkPelatihanPeserta::create([
            'blk_pelatihan_id' => $pelatihanB->id,
            'pencari_id' => $pencari->id,
            'status_pendaftaran' => 1,
            'name' => 'Peserta Riwayat',
            'ktp' => '6401010101010888',
            'created_by' => $pencariUser->id,
        ]);

        $lowongan = Lowongan::create([
            'jabatan_id' => 1,
            'sektor_id' => 1,
            'tanggal_start' => now()->toDateString(),
            'tanggal_end' => now()->addDays(14)->toDateString(),
            'judul_lowongan' => 'Operator Alat Berat',
            'kabkota_id' => 6472,
            'lokasi_penempatan_text' => 'Samarinda',
            'jumlah_pria' => 2,
            'jumlah_wanita' => 0,
            'pendidikan_id' => 3,
            'jurusan_id' => 1,
            'marital_id' => 'B',
            'acc_by' => 1,
            'acc_by_role' => 1,
            'posted_by' => $pencariUser->id,
            'tipe_lowongan' => 0,
        ]);
        Lamaran::create([
            'pencari_id' => $pencariUser->id,
            'lowongan_id' => $lowongan->id,
            'kabkota_penempatan_id' => 6472,
            'progres_id' => 4,
            'keterangan' => null,
        ]);

        $this->actingAs($admin)
            ->get(route('blk.pelatihan.peserta.show', [$pelatihanB->id, $peserta->id]))
            ->assertOk()
            ->assertSee('Peserta Riwayat')
            ->assertSee('6401010101010888')
            ->assertSee('Pelatihan Las')
            ->assertSee('Pelatihan Operator')
            ->assertSee('Riwayat Pelatihan BLK')
            ->assertSee('Riwayat Melamar Kerja')
            ->assertSee('Operator Alat Berat');
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

    public function test_admin_balai_can_create_admin_and_petugas_own_blk_only(): void
    {
        EtamBlk::create([
            'nama_lembaga' => 'BLK Industri Balikpapan',
            'tipe_lembaga' => 1,
            'provinsi_id' => 64,
            'kode_struktur' => '34',
        ]);

        $admin = $this->makeUser('admin-balai', [
            'kode_struktur' => '34',
            'lokasi_kerja' => '64',
            'name' => 'Admin BLK Industri Balikpapan',
        ]);

        $this->actingAs($admin)
            ->get(route('blk.users.index'))
            ->assertOk()
            ->assertSee('Kelola User BLK')
            ->assertSee('Admin')
            ->assertSee('Petugas')
            ->assertDontSee('kepala-balai', false);

        $email = 'petugas.blk.'.uniqid().'@example.com';
        $whatsapp = '08'.fake()->unique()->numerify('##########');

        $this->actingAs($admin)->postJson(route('blk.users.store'), [
            'name' => 'Petugas BLK Balikpapan',
            'email' => $email,
            'whatsapp' => $whatsapp,
            'role' => 'petugas-balai',
            'kode_struktur' => '35',
            'blk_id' => 999,
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'kode_struktur' => '34',
            'lokasi_kerja' => '64',
        ]);

        $created = User::where('email', $email)->first();
        $this->assertTrue($created->hasRole('petugas-balai'));
        $this->assertFalse($created->hasRole('kepala-balai'));

        $adminEmail = 'admin2.blk.'.uniqid().'@example.com';
        $this->actingAs($admin)->postJson(route('blk.users.store'), [
            'name' => 'Admin BLK Kedua',
            'email' => $adminEmail,
            'whatsapp' => '08'.fake()->unique()->numerify('##########'),
            'role' => 'admin-balai',
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertTrue(User::where('email', $adminEmail)->first()->hasRole('admin-balai'));

        $this->actingAs($admin)->postJson(route('blk.users.store'), [
            'name' => 'Kepala Tidak Boleh',
            'email' => 'kepala.'.uniqid().'@example.com',
            'whatsapp' => '08'.fake()->unique()->numerify('##########'),
            'role' => 'kepala-balai',
        ])->assertOk()->assertJson(['success' => false]);

        $this->actingAs($admin)
            ->putJson(route('blk.users.update', 1), [
                'name' => 'X',
                'email' => 'x@example.com',
                'whatsapp' => '08111',
                'tipe_akun' => 3,
            ])
            ->assertStatus(403);

        $this->actingAs($admin)
            ->deleteJson(route('blk.users.destroy', 1))
            ->assertStatus(403);
    }

    public function test_admin_balai_only_lists_users_of_own_blk(): void
    {
        $adminA = $this->makeUser('admin-balai', [
            'kode_struktur' => '34',
            'lokasi_kerja' => '64',
            'name' => 'Admin Balikpapan',
        ]);
        $this->makeUser('petugas-balai', [
            'kode_struktur' => '34',
            'lokasi_kerja' => '64',
            'name' => 'Petugas Balikpapan',
        ]);
        $this->makeUser('kepala-balai', [
            'kode_struktur' => '34',
            'lokasi_kerja' => '64',
            'name' => 'Kepala Balikpapan',
        ]);
        $this->makeUser('petugas-balai', [
            'kode_struktur' => '35',
            'lokasi_kerja' => '64',
            'name' => 'Petugas Bontang',
        ]);

        $response = $this->actingAs($adminA)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('blk.users.index'));

        $response->assertOk();
        $names = collect($response->json('data'))->pluck('user_name');
        $this->assertTrue($names->contains('Admin Balikpapan'));
        $this->assertTrue($names->contains('Petugas Balikpapan'));
        $this->assertFalse($names->contains('Kepala Balikpapan'));
        $this->assertFalse($names->contains('Petugas Bontang'));
    }

    public function test_kepala_and_petugas_cannot_manage_user_blk(): void
    {
        $kepala = $this->makeUser('kepala-balai', [
            'kode_struktur' => '34',
            'lokasi_kerja' => '64',
        ]);
        $this->actingAs($kepala)->get(route('blk.users.index'))->assertStatus(403);

        $petugas = $this->makeUser('petugas-balai', [
            'kode_struktur' => '34',
            'lokasi_kerja' => '64',
        ]);
        $this->actingAs($petugas)->get(route('blk.users.index'))->assertStatus(403);
    }

    public function test_kepala_balai_can_manage_pelatihan_own_blk_only(): void
    {
        $blkSendiri = EtamBlk::create([
            'nama_lembaga' => 'BLK Industri Balikpapan',
            'tipe_lembaga' => 1,
            'provinsi_id' => 64,
            'kode_struktur' => '34',
        ]);
        $blkLain = EtamBlk::create([
            'nama_lembaga' => 'BLK Industri Bontang',
            'tipe_lembaga' => 1,
            'provinsi_id' => 64,
            'kode_struktur' => '35',
        ]);

        $pelatihanSendiri = $this->makePelatihan($blkSendiri->id, EtamBlkPelatihan::UNTUK_PENCARI);
        $pelatihanLain = $this->makePelatihan($blkLain->id, EtamBlkPelatihan::UNTUK_PENCARI);

        $kepala = $this->makeUser('kepala-balai', [
            'kode_struktur' => '34',
            'lokasi_kerja' => '64',
        ]);

        $this->actingAs($kepala)
            ->get(route('blk.pelatihan.edit', $pelatihanSendiri->id))
            ->assertOk();

        $this->actingAs($kepala)
            ->get(route('blk.pelatihan.edit', $pelatihanLain->id))
            ->assertStatus(403);

        $this->actingAs($kepala)
            ->get(route('blk.lembaga.index'))
            ->assertStatus(403);
    }

    public function test_petugas_balai_cannot_access_other_blk_pelatihan(): void
    {
        $blk = EtamBlk::create([
            'nama_lembaga' => 'BLK Bontang',
            'tipe_lembaga' => 1,
            'provinsi_id' => 64,
            'kode_struktur' => '35',
        ]);
        $pelatihan = $this->makePelatihan($blk->id, EtamBlkPelatihan::UNTUK_PENCARI);

        $petugas = $this->makeUser('petugas-balai', [
            'kode_struktur' => '34',
            'lokasi_kerja' => '64',
        ]);

        $this->actingAs($petugas)
            ->get(route('blk.pelatihan.peserta', $pelatihan->id))
            ->assertStatus(403);
    }

    public function test_wawancara_template_is_scoped_per_balai(): void
    {
        $blkA = EtamBlk::create([
            'nama_lembaga' => 'BLK A',
            'tipe_lembaga' => 1,
            'provinsi_id' => 64,
            'kode_struktur' => '34',
        ]);
        $blkB = EtamBlk::create([
            'nama_lembaga' => 'BLK B',
            'tipe_lembaga' => 1,
            'provinsi_id' => 64,
            'kode_struktur' => '35',
        ]);

        $adminA = $this->makeUser('admin-balai', [
            'kode_struktur' => '34',
            'lokasi_kerja' => '64',
        ]);
        $adminB = $this->makeUser('admin-balai', [
            'kode_struktur' => '35',
            'lokasi_kerja' => '64',
        ]);

        $this->actingAs($adminA)->post(route('blk.form.store', 'wawancara'), [
            'blk_id' => $blkA->id,
            'nama' => 'Wawancara Balai A',
            'pertanyaan' => [
                ['teks' => 'Apa motivasi Anda?', 'jenis_pertanyaan' => 1, 'wajib' => 1],
            ],
        ])->assertRedirect(route('blk.form.index', 'wawancara'));

        $formA = EtamBlkForm::where('nama', 'Wawancara Balai A')->first();
        $this->assertNotNull($formA);

        $this->actingAs($adminB)
            ->get(route('blk.form.edit', ['wawancara', $formA->id]))
            ->assertStatus(403);

        $hidden = $this->actingAs($adminB)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('blk.form.index', 'wawancara'));
        $hidden->assertOk();
        $this->assertStringNotContainsString('Wawancara Balai A', $hidden->getContent());
    }

    public function test_pelatihan_can_attach_form_templates(): void
    {
        $blk = EtamBlk::create([
            'nama_lembaga' => 'BLK Form',
            'tipe_lembaga' => 1,
            'provinsi_id' => 64,
            'kode_struktur' => '34',
        ]);
        $wawancara = $this->makeForm($blk->id, EtamBlkForm::JENIS_WAWANCARA, 'Template Wawancara');
        $pretest = $this->makeForm($blk->id, EtamBlkForm::JENIS_PRETEST, 'Template Pretest');
        $admin = $this->makeUser('admin-balai', [
            'kode_struktur' => '34',
            'lokasi_kerja' => '64',
        ]);

        $this->actingAs($admin)->post(route('blk.pelatihan.store'), [
            'pelatihan_untuk' => 0,
            'blk_id' => $blk->id,
            'nama_pelatihan' => 'Pelatihan dengan Form',
            'sumber_pembiayaan' => 0,
            'tanggal_pendaftaran' => now()->toDateString(),
            'tanggal_pendaftaran_selesai' => now()->addDays(7)->toDateString(),
            'tanggal_pelaksanaan' => now()->addDays(10)->toDateString(),
            'tanggal_pelaksanaan_selesai' => now()->addDays(20)->toDateString(),
            'tipe_pelatihan' => 0,
            'status' => 1,
            'wawancara_form_id' => $wawancara->id,
            'pretest_form_id' => $pretest->id,
        ])->assertRedirect(route('blk.pelatihan.index'));

        $this->assertDatabaseHas('etam_blk_pelatihan', [
            'nama_pelatihan' => 'Pelatihan dengan Form',
            'wawancara_form_id' => $wawancara->id,
            'pretest_form_id' => $pretest->id,
        ]);
    }

    public function test_admin_fills_wawancara_and_peserta_fills_pretest_with_snapshot(): void
    {
        $blk = EtamBlk::create([
            'nama_lembaga' => 'BLK Snapshot',
            'tipe_lembaga' => 1,
            'provinsi_id' => 64,
            'kode_struktur' => '34',
        ]);
        $wawancara = $this->makeForm($blk->id, EtamBlkForm::JENIS_WAWANCARA, 'Wawancara Snapshot');
        $pretest = $this->makeForm($blk->id, EtamBlkForm::JENIS_PRETEST, 'Pretest Snapshot');
        $pelatihan = $this->makePelatihan($blk->id, EtamBlkPelatihan::UNTUK_PENCARI);
        $pelatihan->update([
            'wawancara_form_id' => $wawancara->id,
            'pretest_form_id' => $pretest->id,
        ]);

        $admin = $this->makeUser('admin-balai', [
            'kode_struktur' => '34',
            'lokasi_kerja' => '64',
        ]);
        $pencariUser = $this->makeUser('pencari-kerja');
        $pencari = $this->makePencari($pencariUser->id, [
            'ktp' => '6401010101010777',
            'name' => 'Peserta Form',
        ]);

        $this->actingAs($pencariUser)
            ->post(route('blk.pelatihan.daftar.store', $pelatihan->id))
            ->assertRedirect(route('blk.pelatihan.pretest', $pelatihan->id));

        $peserta = EtamBlkPelatihanPeserta::where('blk_pelatihan_id', $pelatihan->id)
            ->where('pencari_id', $pencari->id)
            ->first();
        $this->assertNotNull($peserta);

        $this->actingAs($admin)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('blk.pelatihan.peserta', $pelatihan->id))
            ->assertOk()
            ->assertSee('Isi Wawancara', false)
            ->assertSee('Lihat Pretest', false);

        $this->actingAs($admin)
            ->get(route('blk.pelatihan.peserta.wawancara', [$pelatihan->id, $peserta->id]))
            ->assertOk();

        $wawancaraJawaban = EtamBlkPelatihanJawaban::where('jenis', 'wawancara')
            ->where('blk_peserta_id', $peserta->id)
            ->first();
        $this->assertNotNull($wawancaraJawaban);
        $this->assertSame('Apa motivasi Anda?', $wawancaraJawaban->pertanyaan);

        $this->actingAs($admin)->post(
            route('blk.pelatihan.peserta.wawancara.store', [$pelatihan->id, $peserta->id]),
            [
                'submit' => 1,
                'jawaban' => [$wawancaraJawaban->id => 'Ingin kerja'],
            ]
        )->assertRedirect(route('blk.pelatihan.peserta.wawancara', [$pelatihan->id, $peserta->id]));

        $this->actingAs($pencariUser)
            ->get(route('blk.pelatihan.pretest', $pelatihan->id))
            ->assertOk();

        $pretestJawaban = EtamBlkPelatihanJawaban::where('jenis', 'pretest')
            ->where('blk_peserta_id', $peserta->id)
            ->first();
        $this->assertNotNull($pretestJawaban);

        $this->actingAs($pencariUser)->post(route('blk.pelatihan.pretest.store', $pelatihan->id), [
            'submit' => 1,
            'jawaban' => [$pretestJawaban->id => 'Jawaban pretest'],
        ])->assertRedirect(route('blk.pelatihan.pretest', $pelatihan->id));

        EtamBlkFormPertanyaan::where('form_id', $pretest->id)->update([
            'pertanyaan' => 'Pertanyaan sudah diubah',
        ]);

        $this->assertDatabaseHas('etam_blk_pelatihan_jawaban', [
            'id' => $pretestJawaban->id,
            'pertanyaan' => 'Apa motivasi Anda?',
            'jawaban' => 'Jawaban pretest',
        ]);
        $this->assertNotNull($pretestJawaban->fresh()->submitted_at);

        $this->actingAs($pencariUser)
            ->get(route('blk.pelatihan.peserta.wawancara', [$pelatihan->id, $peserta->id]))
            ->assertStatus(403);
    }

    private function makeForm(int $blkId, string $jenis, string $nama): EtamBlkForm
    {
        $form = EtamBlkForm::create([
            'blk_id' => $blkId,
            'jenis' => $jenis,
            'nama' => $nama,
        ]);
        EtamBlkFormPertanyaan::create([
            'form_id' => $form->id,
            'urutan' => 1,
            'jenis_pertanyaan' => 1,
            'pertanyaan' => 'Apa motivasi Anda?',
            'wajib' => 1,
        ]);

        return $form;
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
