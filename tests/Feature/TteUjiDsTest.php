<?php

namespace Tests\Feature;

use App\Models\BLK\EtamBlk;
use App\Models\BLK\EtamBlkPelatihan;
use App\Models\BLK\EtamBlkPelatihanPeserta;
use App\Models\EtamTte;
use App\Models\EtamTteDsUji;
use App\Models\User;
use App\Models\UserPencari;
use App\Services\Tte\DsClient;
use App\Services\Tte\TteQueue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TteUjiDsTest extends TestCase
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

        config([
            'tte.ds.demo' => false,
            'tte.ds.url' => 'https://ds.kaltimprov.go.id/api',
            'tte.ds.token' => 'token-uji',
            'tte.ds.template' => 'tmpl-uji',
        ]);
    }

    public function test_super_admin_bisa_buka_form_uji_ds(): void
    {
        $admin = $this->makeUser('super-admin');

        $this->actingAs($admin)
            ->get(route('tte.uji-ds'))
            ->assertOk()
            ->assertSee('Jalankan 3 langkah')
            ->assertSee('name="passphrase"', false)
            ->assertDontSee('Tidak boleh disalin tempel');
    }

    public function test_kepala_balai_tidak_bisa_buka_uji_ds(): void
    {
        $kepala = $this->makeUser('kepala-balai', ['kode_struktur' => '34', 'lokasi_kerja' => '64']);

        $this->actingAs($kepala)
            ->get(route('tte.uji-ds'))
            ->assertStatus(403);
    }

    public function test_uji_ds_tetap_simpan_kode_meski_passphrase_salah(): void
    {
        Http::fake(function ($request) {
            if (str_contains($request->url(), '/ttdds')) {
                return Http::response([
                    'success' => true,
                    'data' => ['kode' => 'KODEUJI99'],
                    'message' => 'Berhasil Mengirim Dokumen',
                ], 200);
            }
            if (str_contains($request->url(), '/sign')) {
                return Http::response([
                    'success' => false,
                    'message' => 'Passphrase salah',
                ], 401);
            }

            return Http::response(['success' => false, 'message' => 'Tidak boleh unduh'], 400);
        });

        $admin = $this->makeUser('super-admin');
        $pdf = UploadedFile::fake()->createWithContent(
            'sertifikat.pdf',
            "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF\n"
        );

        $this->actingAs($admin)
            ->post(route('tte.uji-ds.kirim'), [
                'pdf' => $pdf,
                'nik' => '6401010101010001',
                'passphrase' => 'bukan-passphrase',
                'keterangan' => 'Uji simpan kode',
            ])
            ->assertRedirect(route('tte.uji-ds'));

        $row = EtamTteDsUji::query()->latest('id')->first();
        $this->assertNotNull($row);
        $this->assertSame('KODEUJI99', $row->kode_ds, (string) $row->kirim_pesan);
        $this->assertTrue((bool) $row->kirim_ok, (string) $row->kirim_pesan);
        $this->assertFalse((bool) $row->sign_ok);
        $this->assertSame('6401010101010001', $row->nik);
        $this->assertStringContainsString('KODEUJI99', (string) $row->kirim_body);
        $this->assertStringContainsString('Passphrase salah', (string) $row->sign_body);

        Http::assertSent(fn ($request) => str_contains($request->url(), '/ttdds'));
        Http::assertSent(fn ($request) => str_contains($request->url(), '/sign'));
        Http::assertNotSent(fn ($request) => str_contains($request->url(), '/dokumen'));
    }

    public function test_tte_antrean_simpan_kode_ds_sebelum_sign_gagal(): void
    {
        Http::fake(function ($request) {
            if (str_contains($request->url(), '/ttdds')) {
                return Http::response([
                    'success' => true,
                    'data' => ['kode' => 'KODEANTREAN1'],
                    'message' => 'Berhasil Mengirim Dokumen',
                ], 200);
            }

            return Http::response(['success' => false, 'message' => 'Passphrase salah'], 401);
        });

        $peserta = $this->buatPesertaBlk('34', 'Simpan Kode DS');
        $tte = app(TteQueue::class)->ajukanBlkUptd($peserta);
        Storage::disk('local')->put($tte->path_file_before, "%PDF-1.4\nuji\n%%EOF\n");
        $kepala = $this->makeUser('kepala-balai', ['kode_struktur' => '34', 'lokasi_kerja' => '64']);

        $this->actingAs($kepala)
            ->post(route('tte.tandatangani'), ['tte_ids' => [$tte->id], 'passphrase' => 'salah'])
            ->assertStatus(422)
            ->assertJsonPath('kode', DsClient::PASSPHRASE_SALAH);

        $this->assertDatabaseHas('etam_tte', [
            'id' => $tte->id,
            'status' => EtamTte::STATUS_MENUNGGU,
            'kode_ds' => 'KODEANTREAN1',
        ]);
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
