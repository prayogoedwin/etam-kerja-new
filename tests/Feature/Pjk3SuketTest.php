<?php

namespace Tests\Feature;

use App\Models\Pengawasan\K3\EtamPengawasanK3Kategori;
use App\Models\Pengawasan\Pjk3\EtamPjk3Ajuan;
use App\Models\Pengawasan\Pjk3\EtamPjk3Suket;
use App\Models\User;
use App\Models\UserPenyedia;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Pjk3SuketTest extends TestCase
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

    public function test_non_pjk3_cannot_open_pengajuan_suket(): void
    {
        $penyedia = $this->makePenyediaUser('PT Bukan PJK3');

        $this->actingAs($penyedia)
            ->get(route('pengawasan.suket.penyedia.pengajuan.index'))
            ->assertForbidden();

        $this->actingAs($penyedia)
            ->get(route('pengawasan.suket.penyedia.terbit.index'))
            ->assertOk()
            ->assertSee('Suket Layak K3')
            ->assertDontSee('Pengajuan Suket Layak K3 oleh PJK3');
    }

    public function test_pjk3_can_submit_and_inspected_company_sees_draft_after_analisis(): void
    {
        $pjk3 = $this->makePjk3User();
        $uji = $this->makePenyediaUser('PT Perusahaan Diuji');
        $kategori = $this->kategori();

        $this->actingAs($pjk3)
            ->get(route('pengawasan.suket.penyedia.pengajuan.create'))
            ->assertOk()
            ->assertSee('Perusahaan yang diperiksa');

        $this->actingAs($pjk3)
            ->postJson(route('pengawasan.suket.penyedia.pengajuan.store'), $this->payload($uji->id, $kategori->id))
            ->assertOk()
            ->assertJson(['success' => true]);

        $suket = EtamPjk3Suket::query()->where('pjk3_penyedia_id', $pjk3->id)->first();
        $this->assertNotNull($suket);
        $this->assertSame(EtamPjk3Suket::STATUS_DIAJUKAN, (int) $suket->status);

        $this->actingAs($uji)
            ->get(route('pengawasan.suket.penyedia.terbit.show', $suket->id))
            ->assertNotFound();

        $pengawas = $this->makeUser('petugas-bidang', ['kode_struktur' => '21']);
        $kasi = $this->makeUser('kepala-seksi', ['kode_struktur' => '21']);

        $this->actingAs($kasi)
            ->postJson(route('pengawasan.suket.admin.disposisi', $suket->id), [
                'pengawas_id' => $pengawas->id,
                'catatan_disposisi' => 'Mohon cek dokumen',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $suket->refresh();
        $this->assertSame(EtamPjk3Suket::STATUS_DISPO, (int) $suket->status);
        $this->assertSame($pengawas->id, (int) $suket->pengawas_id);

        $this->actingAs($pengawas)
            ->postJson(route('pengawasan.suket.admin.analisis', $suket->id), [
                'tanggal_pemeriksaan' => now()->toDateString(),
                'is_layak' => 1,
                'hasil_analisis' => 'Alat memenuhi syarat K3.',
                'rekomendasi' => 'Dapat dioperasikan.',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $suket->refresh();
        $this->assertSame(EtamPjk3Suket::STATUS_DRAFT, (int) $suket->status);
        $this->assertSame(1, (int) $suket->is_layak);
        $this->assertMatchesRegularExpression(
            '/^'.preg_quote((string) $kategori->kode, '/').'\.\d{3}\/SUKET-K3\/'.now()->year.'$/',
            (string) $suket->nomor_suket
        );

        $this->actingAs($uji)
            ->get(route('pengawasan.suket.penyedia.terbit.show', $suket->id))
            ->assertOk()
            ->assertSee('draft')
            ->assertSee($suket->nomor_suket);

        $this->actingAs($uji)
            ->get(route('pengawasan.suket.penyedia.cetak-lhpp', $suket->id))
            ->assertOk()
            ->assertSee('Laporan Hasil Pemeriksaan')
            ->assertSee('LAYAK');

        $this->actingAs($pengawas)
            ->post(route('pengawasan.suket.admin.tte-pengawas', $suket->id))
            ->assertRedirect(route('pengawasan.suket.admin.show', $suket->id));

        $suket->refresh();
        $this->assertSame(EtamPjk3Suket::STATUS_TTD_PENGAWAS, (int) $suket->status);

        $kadis = $this->makeUser('eksekutif-provinsi');
        $this->actingAs($kadis)
            ->post(route('pengawasan.suket.admin.tte-kadis', $suket->id))
            ->assertRedirect(route('pengawasan.suket.admin.show', $suket->id));

        $suket->refresh();
        $this->assertSame(EtamPjk3Suket::STATUS_TTD_KADIS, (int) $suket->status);

        $this->actingAs($uji)
            ->get(route('pengawasan.suket.penyedia.cetak-suket', $suket->id))
            ->assertOk()
            ->assertSee('SURAT KETERANGAN LAYAK K3')
            ->assertDontSee('DRAFT');
    }

    public function test_tidak_layak_also_creates_suket_draft(): void
    {
        $pjk3 = $this->makePjk3User();
        $uji = $this->makePenyediaUser('PT Tidak Layak');
        $kategori = $this->kategori();
        $pengawas = $this->makeUser('petugas-bidang', ['kode_struktur' => '21']);
        $kasi = $this->makeUser('kepala-seksi', ['kode_struktur' => '21']);

        $this->actingAs($pjk3)
            ->postJson(route('pengawasan.suket.penyedia.pengajuan.store'), $this->payload($uji->id, $kategori->id))
            ->assertOk();

        $suket = EtamPjk3Suket::query()->where('pjk3_penyedia_id', $pjk3->id)->first();

        $this->actingAs($kasi)
            ->postJson(route('pengawasan.suket.admin.disposisi', $suket->id), ['pengawas_id' => $pengawas->id])
            ->assertOk();

        $this->actingAs($pengawas)
            ->postJson(route('pengawasan.suket.admin.analisis', $suket->id), [
                'tanggal_pemeriksaan' => now()->toDateString(),
                'is_layak' => 0,
                'hasil_analisis' => 'Tidak memenuhi syarat.',
            ])
            ->assertOk();

        $suket->refresh();
        $this->assertSame(0, (int) $suket->is_layak);
        $this->assertSame(EtamPjk3Suket::STATUS_DRAFT, (int) $suket->status);

        $this->actingAs($uji)
            ->get(route('pengawasan.suket.penyedia.cetak-suket', $suket->id))
            ->assertOk()
            ->assertSee('SURAT KETERANGAN TIDAK LAYAK K3');
    }

    public function test_admin_bidang_lain_cannot_access_suket(): void
    {
        $admin = $this->makeUser('admin-bidang', ['kode_struktur' => '41']);

        $this->actingAs($admin)
            ->get(route('pengawasan.suket.admin.pengajuan'))
            ->assertForbidden();
    }

    public function test_pjk3_inspects_account_only_company_and_not_self(): void
    {
        $pjk3 = $this->makePjk3User('PT PJK3 Pemeriksa');
        $akunSaja = $this->makePenyediaUser('PT Hanya Punya Akun');
        $pjk3Lain = $this->makePjk3User('PT PJK3 Lain');
        $kategori = $this->kategori();

        $this->actingAs($pjk3)
            ->get(route('pengawasan.suket.penyedia.pengajuan.create'))
            ->assertOk()
            ->assertSee('dua peran')
            ->assertSee('PT Hanya Punya Akun')
            ->assertSee('PT PJK3 Lain')
            ->assertDontSee('PT PJK3 Pemeriksa');

        $this->actingAs($pjk3)
            ->postJson(route('pengawasan.suket.penyedia.pengajuan.store'), $this->payload($pjk3->id, $kategori->id))
            ->assertOk()
            ->assertJsonPath('success', false);

        $this->actingAs($pjk3)
            ->postJson(route('pengawasan.suket.penyedia.pengajuan.store'), $this->payload($akunSaja->id, $kategori->id))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->actingAs($pjk3)
            ->postJson(route('pengawasan.suket.penyedia.pengajuan.store'), $this->payload($pjk3Lain->id, $kategori->id))
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_pjk3_sees_pengajuan_menu(): void
    {
        $pjk3 = $this->makePjk3User();

        $this->actingAs($pjk3)
            ->get(route('pengawasan.suket.penyedia.terbit.index'))
            ->assertOk()
            ->assertSee('Pengajuan Suket Layak K3 oleh PJK3')
            ->assertSee('Pendaftaran PJK3');
    }

    private function kategori(): EtamPengawasanK3Kategori
    {
        $kategori = EtamPengawasanK3Kategori::query()->first();
        if (! $kategori) {
            return EtamPengawasanK3Kategori::create([
                'nama' => 'Listrik',
                'kode' => 'LIS',
            ]);
        }

        if (! $kategori->kode) {
            $kategori->update(['kode' => 'LIS']);
        }

        return $kategori->fresh();
    }

    private function payload(int $perusahaanId, int $kategoriId): array
    {
        return [
            'perusahaan_id' => $perusahaanId,
            'kategori_id' => $kategoriId,
            'nama_alat' => 'Lift Gedung A',
            'lokasi_alat' => 'Samarinda',
            'kapasitas_alat' => '1000 Kg',
            'jumlah_unit' => 1,
            'tipe_surat_permohonan' => 'link',
            'link_surat_permohonan' => 'https://example.com/permohonan',
            'tipe_lhpp' => 'link',
            'link_lhpp' => 'https://example.com/lhpp',
            'tipe_skp_pjk3' => 'link',
            'link_skp_pjk3' => 'https://example.com/skp-pjk3',
            'tipe_ahli_k3' => 'link',
            'link_ahli_k3' => 'https://example.com/ahli-k3',
        ];
    }

    private function makePjk3User(string $name = 'PT PJK3 Aktif'): User
    {
        $user = $this->makePenyediaUser($name);
        EtamPjk3Ajuan::create([
            'penyedia_id' => $user->id,
            'tanggal_ajuan' => now()->toDateString(),
            'status_verifikasi' => EtamPjk3Ajuan::STATUS_ACC,
            'masa_berlaku' => now()->addYear()->toDateString(),
            'dokumen' => [
                'sk_pjk3' => ['tipe' => 'link', 'value' => 'https://example.com/sk'],
                'sk_ahli' => ['tipe' => 'link', 'value' => 'https://example.com/ahli'],
                'wlkp' => ['tipe' => 'link', 'value' => 'https://example.com/wlkp'],
                'bpjs' => ['tipe' => 'link', 'value' => 'https://example.com/bpjs'],
            ],
            'created_by' => $user->id,
        ]);

        return $user;
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

    private function makePenyediaUser(string $name = 'PT Demo'): User
    {
        $user = $this->makeUser('penyedia-kerja');
        UserPenyedia::create([
            'user_id' => $user->id,
            'name' => $name,
            'nib' => fake()->unique()->numerify('##########'),
            'id_sektor' => 1,
            'id_provinsi' => 64,
            'id_kota' => 6472,
            'id_kecamatan' => 6472010,
            'status_id' => 1,
            'posted_by' => $user->id,
        ]);

        return $user;
    }
}
