<?php

namespace Tests\Feature;

use App\Models\BLK\EtamBlk;
use App\Models\BLK\EtamBlkPelatihan;
use App\Models\BLK\EtamBlkPelatihanPeserta;
use App\Models\EtamTte;
use App\Models\EtamTteLog;
use App\Models\User;
use App\Models\UserPencari;
use App\Services\Tte\BsreClient;
use App\Services\Tte\TteQueue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;
use ZipArchive;

class TteModuleTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        foreach (['super-admin', 'admin-provinsi', 'pencari-kerja', 'kepala-balai', 'admin-balai', 'eksekutif-provinsi'] as $name) {
            Role::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }
    }

    public function test_enqueue_blk_muncul_di_kepala_balai_bukan_kadis(): void
    {
        $peserta = $this->buatPesertaBlk('34', 'Peserta Antrean Balai');
        app(TteQueue::class)->ajukanBlkUptd($peserta);

        $kepala = $this->makeUser('kepala-balai', ['kode_struktur' => '34', 'lokasi_kerja' => '64']);
        $kadis = $this->makeUser('eksekutif-provinsi');

        $judulBalai = collect($this->actingAs($kepala)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('tte.menunggu'))
            ->assertOk()
            ->json('data'))->pluck('judul');

        $this->assertTrue($judulBalai->contains(fn ($judul) => str_contains((string) $judul, 'Peserta Antrean Balai')));

        $judulKadis = collect($this->actingAs($kadis)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('tte.menunggu'))
            ->assertOk()
            ->json('data'))->pluck('judul');

        $this->assertFalse($judulKadis->contains(fn ($judul) => str_contains((string) $judul, 'Peserta Antrean Balai')));
    }

    public function test_kepala_balai_lain_tidak_melihat_antrean(): void
    {
        $peserta = $this->buatPesertaBlk('34', 'Hanya Balikpapan');
        app(TteQueue::class)->ajukanBlkUptd($peserta);

        $lain = $this->makeUser('kepala-balai', ['kode_struktur' => '35', 'lokasi_kerja' => '64']);

        $judul = collect($this->actingAs($lain)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('tte.menunggu'))
            ->json('data'))->pluck('judul');

        $this->assertFalse($judul->contains(fn ($item) => str_contains((string) $item, 'Hanya Balikpapan')));
    }

    public function test_tte_balai_membuat_antrean_kadis(): void
    {
        $peserta = $this->buatPesertaBlk('34', 'Alur Dua Jenjang');
        $tte = app(TteQueue::class)->ajukanBlkUptd($peserta);
        $kepala = $this->makeUser('kepala-balai', ['kode_struktur' => '34', 'lokasi_kerja' => '64']);
        $kadis = $this->makeUser('eksekutif-provinsi');

        $this->actingAs($kepala)
            ->post(route('tte.tandatangani'), ['tte_ids' => [$tte->id], 'passphrase' => 'rahasia-tte'])
            ->assertOk()
            ->assertJsonPath('berhasil', 1);

        $this->assertDatabaseHas('etam_blk_pelatihan_peserta', [
            'id' => $peserta->id,
            'status_ttd' => EtamBlkPelatihanPeserta::TTD_TTD_UPTD,
        ]);
        $this->assertTrue(EtamTte::query()
            ->where('nama_modul', 'blk')
            ->where('id_refrensi', $peserta->id)
            ->whereNull('id_struktur')
            ->where('status', EtamTte::STATUS_MENUNGGU)
            ->exists());

        $judulKadis = collect($this->actingAs($kadis)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('tte.menunggu'))
            ->json('data'))->pluck('judul');
        $this->assertTrue($judulKadis->contains(fn ($judul) => str_contains((string) $judul, 'Alur Dua Jenjang')));
    }

    public function test_tte_kadis_mengupdate_status_ttd(): void
    {
        $peserta = $this->buatPesertaBlk('34', 'Selesai Kadis', EtamBlkPelatihanPeserta::TTD_TTD_UPTD);
        $tte = app(TteQueue::class)->ajukan([
            'nama_modul' => 'blk',
            'nama_table' => $peserta->getTable(),
            'id_refrensi' => $peserta->id,
            'kolom_status_refrensi' => 'status_ttd',
            'id_struktur' => null,
            'judul' => 'Sertifikat BLK — Selesai Kadis',
        ]);
        $kadis = $this->makeUser('eksekutif-provinsi');

        $this->actingAs($kadis)
            ->post(route('tte.tandatangani'), ['tte_ids' => [$tte->id], 'passphrase' => 'rahasia-tte'])
            ->assertOk()
            ->assertJsonPath('berhasil', 1);

        $this->assertDatabaseHas('etam_blk_pelatihan_peserta', [
            'id' => $peserta->id,
            'status_ttd' => EtamBlkPelatihanPeserta::TTD_TTD_DINAS,
        ]);
        $this->assertDatabaseHas('etam_tte', [
            'id' => $tte->id,
            'status' => EtamTte::STATUS_BERHASIL,
            'user_id_tte' => $kadis->id,
        ]);
        $this->assertEquals(0, EtamTte::query()
            ->where('nama_modul', 'blk')
            ->where('id_refrensi', $peserta->id)
            ->where('status', EtamTte::STATUS_MENUNGGU)
            ->count());
    }

    public function test_tte_blk_dua_berkas_terpisah_lalu_pencari_unduh_zip(): void
    {
        $peserta = $this->buatPesertaBlk('34', 'Peserta Dua Berkas');
        $tteKompetensi = app(TteQueue::class)->ajukanBlkUptd($peserta);
        $kepala = $this->makeUser('kepala-balai', ['kode_struktur' => '34', 'lokasi_kerja' => '64']);
        $kadis = $this->makeUser('eksekutif-provinsi');
        $pencariUser = User::find($peserta->pencari->user_id);

        $this->assertSame(EtamBlkPelatihanPeserta::TTE_DOK_KOMPETENSI, $tteKompetensi->kode_dokumen);
        Storage::disk('local')->assertExists('tte/blk/'.$peserta->id.'/kompetensi.before.pdf');
        $this->assertSame('tte/blk/'.$peserta->id.'/kompetensi.before.pdf', $tteKompetensi->path_file_before);
        $this->assertStringStartsWith('%PDF', Storage::disk('local')->get($tteKompetensi->path_file_before));

        $this->actingAs($kepala)
            ->post(route('tte.tandatangani'), ['tte_ids' => [$tteKompetensi->id], 'passphrase' => 'rahasia-tte'])
            ->assertOk()
            ->assertJsonPath('berhasil', 1);

        $tteKompetensi->refresh();
        Storage::disk('local')->assertExists($tteKompetensi->path_file_after);
        $this->assertStringStartsWith('%PDF', (string) Storage::disk('local')->get($tteKompetensi->path_file_after));
        Storage::disk('local')->assertExists('tte/blk/'.$peserta->id.'/sertifikat.before.pdf');
        $this->assertDatabaseHas('etam_tte_log', [
            'etam_tte_id' => $tteKompetensi->id,
            'status' => EtamTteLog::STATUS_BERHASIL,
        ]);
        $this->assertDatabaseHas('etam_tte', [
            'nama_modul' => 'blk',
            'id_refrensi' => $peserta->id,
            'kode_dokumen' => EtamBlkPelatihanPeserta::TTE_DOK_SERTIFIKAT,
            'status' => EtamTte::STATUS_MENUNGGU,
        ]);
        $this->assertDatabaseHas('etam_tte', [
            'nama_modul' => 'blk',
            'id_refrensi' => $peserta->id,
            'kode_dokumen' => EtamBlkPelatihanPeserta::TTE_DOK_SERTIFIKAT,
            'path_file_before' => 'tte/blk/'.$peserta->id.'/sertifikat.before.pdf',
        ]);

        $this->actingAs($pencariUser)
            ->get(route('historypelatihan.pencari.sertifikat', $peserta->id))
            ->assertForbidden();

        $options = collect($this->actingAs($pencariUser)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('historypelatihan.pencari.index'))
            ->json('data'))->pluck('options')->implode(' ');
        $this->assertStringNotContainsString('Unduh Sertifikat', $options);

        $tteSertifikat = EtamTte::query()
            ->where('id_refrensi', $peserta->id)
            ->where('kode_dokumen', EtamBlkPelatihanPeserta::TTE_DOK_SERTIFIKAT)
            ->where('status', EtamTte::STATUS_MENUNGGU)
            ->first();

        $this->actingAs($kadis)
            ->post(route('tte.tandatangani'), ['tte_ids' => [$tteSertifikat->id], 'passphrase' => 'rahasia-tte'])
            ->assertOk()
            ->assertJsonPath('berhasil', 1);

        $tteSertifikat->refresh();
        Storage::disk('local')->assertExists($tteSertifikat->path_file_after);
        $this->assertStringStartsWith('%PDF', (string) Storage::disk('local')->get($tteSertifikat->path_file_after));
        $this->assertDatabaseHas('etam_blk_pelatihan_peserta', [
            'id' => $peserta->id,
            'status_ttd' => EtamBlkPelatihanPeserta::TTD_TTD_DINAS,
        ]);

        $options = collect($this->actingAs($pencariUser)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('historypelatihan.pencari.index'))
            ->json('data'))->pluck('options')->implode(' ');
        $this->assertStringContainsString('Unduh Sertifikat', $options);

        $unduh = $this->actingAs($pencariUser)
            ->get(route('historypelatihan.pencari.sertifikat', $peserta->id));
        $unduh->assertOk();
        $unduh->assertDownload('sertifikat-blk-'.$peserta->id.'.zip');

        $zip = new ZipArchive;
        $this->assertTrue($zip->open($unduh->getFile()->getPathname()) === true);
        $this->assertNotFalse($zip->locateName('1-sertifikat-kelulusan.pdf'));
        $this->assertNotFalse($zip->locateName('2-daftar-unit-kompetensi.pdf'));
        $isiSertifikat = $zip->getFromName('1-sertifikat-kelulusan.pdf');
        $isiKompetensi = $zip->getFromName('2-daftar-unit-kompetensi.pdf');
        $zip->close();

        $this->assertStringStartsWith('%PDF', $isiSertifikat);
        $this->assertStringStartsWith('%PDF', $isiKompetensi);
    }

    public function test_modul_tak_terdaftar_tidak_merusak_tabel_sumber(): void
    {
        $peserta = $this->buatPesertaBlk('34', 'Sumber Aman', EtamBlkPelatihanPeserta::TTD_DIAJUKAN_UPTD);
        $statusAwal = (int) $peserta->status_ttd;

        $tte = app(TteQueue::class)->ajukan([
            'nama_modul' => 'modul_tidak_ada',
            'nama_table' => $peserta->getTable(),
            'id_refrensi' => $peserta->id,
            'kolom_status_refrensi' => 'status_ttd',
            'id_struktur' => '34',
            'judul' => 'Dokumen modul asing',
        ]);
        $kepala = $this->makeUser('kepala-balai', ['kode_struktur' => '34', 'lokasi_kerja' => '64']);

        $this->actingAs($kepala)
            ->post(route('tte.tandatangani'), ['tte_ids' => [$tte->id], 'passphrase' => 'rahasia-tte'])
            ->assertOk()
            ->assertJsonPath('gagal', 1);

        $this->assertDatabaseHas('etam_tte', [
            'id' => $tte->id,
            'status' => EtamTte::STATUS_GAGAL,
        ]);
        $this->assertDatabaseHas('etam_blk_pelatihan_peserta', [
            'id' => $peserta->id,
            'status_ttd' => $statusAwal,
        ]);
    }

    public function test_super_admin_melihat_menu_dan_semua_antrean(): void
    {
        $peserta = $this->buatPesertaBlk('34', 'Terlihat Super Admin');
        app(TteQueue::class)->ajukanBlkUptd($peserta);
        $admin = $this->makeUser('super-admin');

        $this->actingAs($admin)
            ->get(route('tte.menunggu'))
            ->assertOk()
            ->assertSee('TTE')
            ->assertSee('Filter modul')
            ->assertSee('blk');

        $judul = collect($this->actingAs($admin)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('tte.menunggu'))
            ->json('data'))->pluck('judul');

        $this->assertTrue($judul->contains(fn ($item) => str_contains((string) $item, 'Terlihat Super Admin')));
    }

    public function test_filter_modul_dari_distinct_etam_tte(): void
    {
        $peserta = $this->buatPesertaBlk('34', 'Dokumen BLK Filter');
        app(TteQueue::class)->ajukanBlkUptd($peserta);
        app(TteQueue::class)->ajukan([
            'nama_modul' => 'hi_pp',
            'nama_table' => 'etam_hi_pp_ajuan',
            'id_refrensi' => 999001,
            'kolom_status_refrensi' => 'status',
            'id_struktur' => null,
            'judul' => 'SK PP Filter',
        ]);
        $admin = $this->makeUser('super-admin');

        $this->actingAs($admin)
            ->get(route('tte.menunggu'))
            ->assertOk()
            ->assertSee('blk')
            ->assertSee('hi_pp');

        $filtered = collect($this->actingAs($admin)
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('tte.menunggu', ['filter_modul' => 'blk']))
            ->json('data'));

        $this->assertTrue($filtered->pluck('judul')->contains(fn ($item) => str_contains((string) $item, 'Dokumen BLK Filter')));
        $this->assertFalse($filtered->pluck('judul')->contains(fn ($item) => str_contains((string) $item, 'SK PP Filter')));
        $this->assertTrue($filtered->every(fn ($row) => ($row['modul'] ?? '') === 'blk'));
    }

    public function test_kelola_admin_nik_tidak_wajib(): void
    {
        $admin = $this->makeUser('super-admin');
        $role = Role::where('name', 'eksekutif-provinsi')->first();

        $this->actingAs($admin)
            ->postJson(route('admin.add'), [
                'name' => 'Kadis Tanpa NIK',
                'email' => 'kadis-nonik@example.test',
                'whatsapp' => '08'.fake()->unique()->numerify('##########'),
                'role_id' => $role->id,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('users', [
            'email' => 'kadis-nonik@example.test',
            'nik' => null,
        ]);
    }

    public function test_kelola_admin_bisa_simpan_nik(): void
    {
        $admin = $this->makeUser('super-admin');
        $role = Role::where('name', 'kepala-balai')->first();

        $this->actingAs($admin)
            ->postJson(route('admin.add'), [
                'name' => 'Kepala Balai NIK',
                'email' => 'kepala-nik@example.test',
                'whatsapp' => '08'.fake()->unique()->numerify('##########'),
                'nik' => '6401010101010007',
                'role_id' => $role->id,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('users', [
            'email' => 'kepala-nik@example.test',
            'nik' => '6401010101010007',
        ]);
    }

    public function test_tte_ditolak_jika_nik_penandatangan_kosong(): void
    {
        $peserta = $this->buatPesertaBlk('34', 'Tanpa NIK');
        $tte = app(TteQueue::class)->ajukanBlkUptd($peserta);
        $kepala = $this->makeUser('kepala-balai', [
            'kode_struktur' => '34',
            'lokasi_kerja' => '64',
            'nik' => null,
        ]);

        $this->actingAs($kepala)
            ->post(route('tte.tandatangani'), ['tte_ids' => [$tte->id], 'passphrase' => 'rahasia-tte'])
            ->assertStatus(422)
            ->assertJsonPath('kode', BsreClient::NIK_KOSONG);

        $this->assertDatabaseHas('etam_tte', [
            'id' => $tte->id,
            'status' => EtamTte::STATUS_MENUNGGU,
        ]);
        $this->assertDatabaseMissing('etam_tte_log', [
            'etam_tte_id' => $tte->id,
        ]);
    }

    public function test_passphrase_salah_tidak_mengubah_status_dan_wajib_ada_log(): void
    {
        $peserta = $this->buatPesertaBlk('34', 'Passphrase Salah');
        $tte = app(TteQueue::class)->ajukanBlkUptd($peserta);
        $kepala = $this->makeUser('kepala-balai', ['kode_struktur' => '34', 'lokasi_kerja' => '64']);

        $this->actingAs($kepala)
            ->post(route('tte.tandatangani'), ['tte_ids' => [$tte->id], 'passphrase' => 'bukan-rahasia'])
            ->assertStatus(422)
            ->assertJsonPath('kode', BsreClient::PASSPHRASE_SALAH);

        $this->assertDatabaseHas('etam_tte', [
            'id' => $tte->id,
            'status' => EtamTte::STATUS_MENUNGGU,
        ]);
        $this->assertDatabaseHas('etam_tte_log', [
            'etam_tte_id' => $tte->id,
            'kode' => BsreClient::PASSPHRASE_SALAH,
            'status' => EtamTteLog::STATUS_GAGAL,
        ]);
    }

    public function test_koneksi_bsre_putus_memberi_notif_dan_log(): void
    {
        config(['tte.bsre.simulate_disconnect' => true]);
        $peserta = $this->buatPesertaBlk('34', 'Koneksi Putus');
        $tte = app(TteQueue::class)->ajukanBlkUptd($peserta);
        $kepala = $this->makeUser('kepala-balai', ['kode_struktur' => '34', 'lokasi_kerja' => '64']);

        $this->actingAs($kepala)
            ->post(route('tte.tandatangani'), ['tte_ids' => [$tte->id], 'passphrase' => 'rahasia-tte'])
            ->assertStatus(422)
            ->assertJsonPath('kode', BsreClient::KONEKSI_PUTUS)
            ->assertJsonFragment(['message' => 'Koneksi ke server BSrE terputus. Proses TTE dihentikan.']);

        $this->assertDatabaseHas('etam_tte', [
            'id' => $tte->id,
            'status' => EtamTte::STATUS_MENUNGGU,
        ]);
        $this->assertDatabaseHas('etam_tte_log', [
            'etam_tte_id' => $tte->id,
            'kode' => BsreClient::KONEKSI_PUTUS,
            'status' => EtamTteLog::STATUS_GAGAL,
        ]);
    }

    public function test_halaman_tte_memuat_passphrase_tanpa_cache_dan_log_menu(): void
    {
        $admin = $this->makeUser('super-admin');

        $this->actingAs($admin)
            ->get(route('tte.menunggu'))
            ->assertOk()
            ->assertSee('id="tte_passphrase"', false)
            ->assertSee('type="password"', false)
            ->assertSee('autocomplete="new-password"', false)
            ->assertSee('data-lpignore="true"', false)
            ->assertSee('readonly', false)
            ->assertSee('Log TTE');
    }

    public function test_halaman_log_tte_dapat_dibuka(): void
    {
        $admin = $this->makeUser('super-admin');

        $this->actingAs($admin)
            ->get(route('tte.log'))
            ->assertOk()
            ->assertSee('Log TTE');
    }

    public function test_qr_verifikasi_mengarah_ke_berkas_asli(): void
    {
        $peserta = $this->buatPesertaBlk('34', 'QR Berkas');
        $tte = app(TteQueue::class)->ajukanBlkUptd($peserta);

        $this->assertNotEmpty($tte->token_verifikasi);
        $this->get(route('tte.berkas', $tte->token_verifikasi))
            ->assertOk()
            ->assertSee('Verifikasi berkas TTE')
            ->assertSee('QR Berkas');
    }

    private function buatPesertaBlk(string $kodeStruktur, string $nama, int $statusTtd = EtamBlkPelatihanPeserta::TTD_DIAJUKAN_UPTD): EtamBlkPelatihanPeserta
    {
        $blk = EtamBlk::create([
            'nama_lembaga' => 'BLK TTE '.$kodeStruktur,
            'tipe_lembaga' => 1,
            'provinsi_id' => 64,
            'kode_struktur' => $kodeStruktur,
        ]);
        $pelatihan = EtamBlkPelatihan::create([
            'pelatihan_untuk' => EtamBlkPelatihan::UNTUK_PENCARI,
            'blk_id' => $blk->id,
            'nama_pelatihan' => 'Pelatihan TTE',
            'sumber_pembiayaan' => 0,
            'tanggal_pendaftaran' => now()->subDay()->toDateString(),
            'tanggal_pendaftaran_selesai' => now()->addDays(7)->toDateString(),
            'tanggal_pelaksanaan' => now()->addDays(10)->toDateString(),
            'tanggal_pelaksanaan_selesai' => now()->addDays(20)->toDateString(),
            'tipe_pelatihan' => 0,
            'status' => EtamBlkPelatihan::STATUS_AKTIF,
            'slug' => 'pelatihan-tte-'.uniqid(),
        ]);

        $user = $this->makeUser('pencari-kerja');
        $pencari = UserPencari::create([
            'user_id' => $user->id,
            'ktp' => '64'.fake()->unique()->numerify('##############'),
            'name' => $nama,
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
            'posted_by' => $user->id,
            'is_diterima' => 0,
            'ex_tambang' => 'N',
        ]);

        return EtamBlkPelatihanPeserta::create([
            'blk_pelatihan_id' => $pelatihan->id,
            'pencari_id' => $pencari->id,
            'status_pendaftaran' => EtamBlkPelatihanPeserta::STATUS_LULUS,
            'status_ttd' => $statusTtd,
            'name' => $nama,
            'ktp' => $pencari->ktp,
            'created_by' => $user->id,
        ]);
    }

    private function makeUser(string $role, array $overrides = []): User
    {
        $defaults = [
            'whatsapp' => '08'.fake()->unique()->numerify('##########'),
            'is_finished' => 1,
        ];
        if (in_array($role, ['kepala-balai', 'eksekutif-provinsi', 'super-admin'], true)
            && ! array_key_exists('nik', $overrides)) {
            $defaults['nik'] = fake()->unique()->numerify('################');
        }

        $user = User::factory()->create(array_merge($defaults, $overrides));
        $user->assignRole($role);

        return $user;
    }
}
