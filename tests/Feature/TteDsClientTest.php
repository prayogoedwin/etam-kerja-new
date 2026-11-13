<?php

namespace Tests\Feature;

use App\Models\BLK\EtamBlk;
use App\Models\BLK\EtamBlkPelatihan;
use App\Models\BLK\EtamBlkPelatihanPeserta;
use App\Models\EtamStruktur;
use App\Models\EtamTte;
use App\Models\User;
use App\Models\UserPencari;
use App\Services\Tte\DsClient;
use App\Services\Tte\TteQueue;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TteDsClientTest extends TestCase
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
            'tte.ds.callback_wait' => 0,
        ]);
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

    public function test_halaman_login_ds_dianggap_token_ditolak(): void
    {
        Http::fake([
            '*' => Http::response(
                '<!DOCTYPE html><html><title>Masuk - Digital Signature Prov. Kaltim</title></html>',
                200
            ),
        ]);

        $hasil = app(DsClient::class)->kirimDokumen("%PDF-1.4\n%%EOF\n", '6401010101010001', 'uji');

        $this->assertFalse($hasil['ok']);
        $this->assertSame(DsClient::GAGAL, $hasil['kode']);
        $this->assertStringContainsString('halaman login', mb_strtolower($hasil['pesan']));
        $this->assertStringContainsString('TTE_DS_TOKEN', $hasil['pesan']);
    }

    public function test_ds_memakai_path_v2(): void
    {
        Http::fake([
            '*' => Http::response([
                'success' => true,
                'data' => ['kode' => 'KODEV2'],
                'message' => 'Berhasil Mengirim Dokumen',
            ], 200),
        ]);

        app(DsClient::class)->kirimDokumen("%PDF-1.4\n%%EOF\n", '6401010101010001', 'uji');

        Http::assertSent(fn ($request) => str_contains($request->url(), '/api/v2/ttdds'));
    }

    public function test_sign_berhasil_meski_callback_ds_gagal_lalu_unduh_pdf(): void
    {
        Http::fake(function ($request) {
            if (str_contains($request->url(), '/ttdds')) {
                return Http::response([
                    'success' => true,
                    'data' => ['kode' => 'KODECB1'],
                    'message' => 'Berhasil Mengirim Dokumen',
                ], 200);
            }
            if (str_contains($request->url(), '/sign')) {
                return Http::response([
                    'success' => false,
                    'message' => 'gagal',
                    'data' => ['catatan' => 'Berhasil Menanda-tangani Dokumen, Gagal Mengirim Dokumen'],
                ], 404);
            }

            return Http::response("%PDF-1.4\nsigned\n%%EOF\n", 200);
        });

        Storage::disk('local')->put('tte/before/uji.pdf', "%PDF-1.4\nuji\n%%EOF\n");

        $hasil = app(DsClient::class)->tandatangani(
            'tte/before/uji.pdf',
            'Bsre2026!@',
            '6401010101010001',
            'uji'
        );

        $this->assertTrue($hasil['ok'], (string) $hasil['pesan']);
        $this->assertSame('KODECB1', $hasil['kode_dokumen']);
        $this->assertNotNull($hasil['pdf']);
        Http::assertSent(fn ($request) => str_contains($request->url(), '/dokumen'));
    }

    public function test_sign_berhasil_kirim_berhasil_tidak_unduh_dokumen(): void
    {
        Http::fake(function ($request) {
            if (str_contains($request->url(), '/ttdds')) {
                return Http::response([
                    'success' => true,
                    'data' => ['kode' => 'KODESEND1'],
                    'message' => 'Berhasil Mengirim Dokumen',
                ], 200);
            }
            if (str_contains($request->url(), '/sign')) {
                return Http::response([
                    'success' => true,
                    'message' => 'Berhasil Menanda-tangani Dokumen',
                ], 200);
            }

            return Http::response('should-not-unduh', 500);
        });

        Storage::disk('local')->put('tte/before/uji.pdf', "%PDF-1.4\nuji\n%%EOF\n");

        $hasil = app(DsClient::class)->tandatangani(
            'tte/before/uji.pdf',
            'Bsre2026!@',
            '6401010101010001',
            'uji'
        );

        $this->assertTrue($hasil['ok'], (string) $hasil['pesan']);
        $this->assertSame('KODESEND1', $hasil['kode_dokumen']);
        $this->assertNull($hasil['pdf']);
        $this->assertTrue($hasil['via_callback'] ?? false);
        Http::assertNotSent(fn ($request) => str_contains($request->url(), '/dokumen'));
    }

    public function test_callback_terima_menyimpan_pdf_dan_selesai(): void
    {
        $peserta = $this->buatPesertaBlk('34', 'Callback Terima');
        $tte = app(TteQueue::class)->ajukanBlkUptd($peserta);
        $tte->kode_ds = 'KODETERIMA1';
        $tte->save();

        $pdf = "%PDF-1.4\nsigned-callback\n%%EOF\n";
        $file = UploadedFile::fake()->createWithContent('sign.pdf', $pdf);

        $this->post('/api/ds/terima', [
            'kode' => 'KODETERIMA1',
            'signpdf' => $file,
        ])->assertOk()->assertJsonPath('status', 'Berhasil');

        $tte->refresh();
        $this->assertSame(EtamTte::STATUS_BERHASIL, (int) $tte->status);
        $this->assertNotNull($tte->path_file_after);
        $this->assertStringContainsString('signed-callback', Storage::disk('local')->get($tte->path_file_after));
        $this->assertSame(EtamBlkPelatihanPeserta::TTD_TTD_UPTD, (int) $peserta->fresh()->status_ttd);
    }

    public function test_callback_terima_idempotent_jika_sudah_berhasil(): void
    {
        $peserta = $this->buatPesertaBlk('34', 'Callback Idempotent');
        $tte = app(TteQueue::class)->ajukanBlkUptd($peserta);
        $tte->kode_ds = 'KODEIDEM1';
        $tte->save();

        $file1 = UploadedFile::fake()->createWithContent('sign.pdf', "%PDF-1.4\npertama\n%%EOF\n");
        $this->post('/api/ds/terima', ['kode' => 'KODEIDEM1', 'signpdf' => $file1])->assertOk();

        $file2 = UploadedFile::fake()->createWithContent('sign.pdf', "%PDF-1.4\nkedua\n%%EOF\n");
        $this->post('/api/ds/terima', ['kode' => 'KODEIDEM1', 'signpdf' => $file2])
            ->assertOk()
            ->assertJsonPath('status', 'Berhasil');

        $tte->refresh();
        $this->assertSame(EtamTte::STATUS_BERHASIL, (int) $tte->status);
        $this->assertStringContainsString('kedua', Storage::disk('local')->get($tte->path_file_after));
        $this->assertSame(EtamBlkPelatihanPeserta::TTD_TTD_UPTD, (int) $peserta->fresh()->status_ttd);
    }

    public function test_sign_berhasil_unduh_timeout_tetap_menunggu_dan_simpan_kode_ds(): void
    {
        Http::fake(function ($request) {
            if (str_contains($request->url(), '/ttdds')) {
                return Http::response([
                    'success' => true,
                    'data' => ['kode' => 'KODETIMEOUT1'],
                    'message' => 'Berhasil Mengirim Dokumen',
                ], 200);
            }
            if (str_contains($request->url(), '/sign')) {
                return Http::response([
                    'success' => false,
                    'message' => 'gagal',
                    'data' => ['catatan' => 'Berhasil Menanda-tangani Dokumen, Gagal Mengirim Dokumen'],
                ], 404);
            }

            throw new ConnectionException('cURL error 28: Operation timed out');
        });

        $peserta = $this->buatPesertaBlk('34', 'Timeout Unduh DS');
        $tte = app(TteQueue::class)->ajukanBlkUptd($peserta);
        Storage::disk('local')->put($tte->path_file_before, "%PDF-1.4\nuji\n%%EOF\n");
        $kepala = $this->makeUser('kepala-balai', ['kode_struktur' => '34', 'lokasi_kerja' => '64']);

        $this->actingAs($kepala)
            ->post(route('tte.tandatangani'), ['tte_ids' => [$tte->id], 'passphrase' => 'Bsre2026!@'])
            ->assertOk()
            ->assertJsonPath('berhasil', 1);

        $this->assertDatabaseHas('etam_tte', [
            'id' => $tte->id,
            'status' => EtamTte::STATUS_MENUNGGU,
            'kode_ds' => 'KODETIMEOUT1',
        ]);
        $this->assertNull($tte->fresh()->path_file_after);
    }

    public function test_super_admin_tarik_berkas_dari_ds(): void
    {
        Http::fake(function ($request) {
            if (str_contains($request->url(), '/dokumen')) {
                return Http::response("%PDF-1.4\nsigned-tarik\n%%EOF\n", 200);
            }

            return Http::response(['success' => false, 'message' => 'tidak dipakai'], 500);
        });

        $peserta = $this->buatPesertaBlk('34', 'Tarik Berkas DS');
        $tte = app(TteQueue::class)->ajukanBlkUptd($peserta);
        $tte->kode_ds = 'KODETARIK1';
        $tte->status = EtamTte::STATUS_GAGAL;
        $tte->save();

        $admin = $this->makeUser('super-admin');

        $this->actingAs($admin)
            ->post(route('tte.tarik', $tte->id))
            ->assertOk()
            ->assertJsonPath('ok', true);

        $tte->refresh();
        $this->assertSame(EtamTte::STATUS_BERHASIL, (int) $tte->status);
        $this->assertNotNull($tte->path_file_after);
        $this->assertStringContainsString('signed-tarik', Storage::disk('local')->get($tte->path_file_after));
        $this->assertSame(EtamBlkPelatihanPeserta::TTD_TTD_UPTD, (int) $peserta->fresh()->status_ttd);
        Http::assertSent(fn ($request) => str_contains($request->url(), '/dokumen')
            && $request['kode'] === 'KODETARIK1');
    }

    public function test_bukan_super_admin_tidak_boleh_tarik(): void
    {
        $peserta = $this->buatPesertaBlk('34', 'Tarik Ditolak');
        $tte = app(TteQueue::class)->ajukanBlkUptd($peserta);
        $tte->kode_ds = 'KODE403';
        $tte->save();
        $kepala = $this->makeUser('kepala-balai', ['kode_struktur' => '34', 'lokasi_kerja' => '64']);

        $this->actingAs($kepala)
            ->post(route('tte.tarik', $tte->id))
            ->assertForbidden();
    }

    public function test_callback_tolak_menandai_gagal(): void
    {
        $peserta = $this->buatPesertaBlk('34', 'Callback Tolak');
        $tte = app(TteQueue::class)->ajukanBlkUptd($peserta);
        $tte->kode_ds = 'KODETOLAK1';
        $tte->save();

        $this->post('/api/ds/tolak', [
            'kode' => 'KODETOLAK1',
            'keterangan' => 'Ditolak operator DS',
        ])->assertOk()->assertJsonPath('status', 'Berhasil');

        $this->assertSame(EtamTte::STATUS_GAGAL, (int) $tte->fresh()->status);
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
        if ($role === 'eksekutif-provinsi' && ! array_key_exists('kode_struktur', $overrides)) {
            $defaults['kode_struktur'] = EtamStruktur::KODE_DINAS;
            $defaults['lokasi_kerja'] = $overrides['lokasi_kerja'] ?? '64';
        }

        $user = User::factory()->create(array_merge($defaults, $overrides));
        $user->assignRole($role);

        return $user;
    }
}
